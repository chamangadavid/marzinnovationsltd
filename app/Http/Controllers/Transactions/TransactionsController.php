<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Transactions\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Milon\Barcode\DNS1D;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TransactionsController extends Controller
{

    public function index()
    {
        return Inertia::render('MyMARZ/Transactions/Index');
    }

    public function getTransactions(Request $request)
    {
        $query = Transaction::query();
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('transactionId', 'like', '%' . $request->search . '%')
                ->orWhere('transactionName', 'like', '%' . $request->search . '%')
                ->orWhere('clientName', 'like', '%' . $request->search . '%')
                ->orWhere('clientEmail', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $transactions = $query->latest()->paginate(10);
        
        return response()->json([
            'data' => $transactions->items(),
            'meta' => [
                'total' => $transactions->total(),
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'transactionName' => 'required|string',
            'clientName' => 'required|string',
            'clientEmail' => 'required|email',
            'clientMobile' => 'nullable|string',
            'clientTpin' => 'nullable|string',
            'clientAddress' => 'nullable|string',
            'status' => 'required|in:Paid,Pending,Partially_Paid',
            'quantity' => 'required|integer|min:1',
            'unitPrice' => 'required|numeric|min:0',
        ]);

        // Generate transaction ID
        $transactionId = 'TRX-' . strtoupper(Str::random(6)) . '-' . now()->format('Ymd');

        $transaction = Transaction::create(array_merge($request->all(), [
            'transactionId' => $transactionId
        ]));
        
        return response()->json($transaction, 201);
    }

    public function destroy($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            $transaction->delete();
            
            return response()->json(['message' => 'Transaction deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete transaction'], 500);
        }
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:Paid,Pending,Partially_Paid',
            'initial_pay' => 'nullable|numeric|min:0|max:'.$transaction->totalAmount
        ]);

        $transaction->update([
            'status' => $request->status,
            'initial_pay' => $request->initial_pay ?? $transaction->initial_pay,
            'balance' => $transaction->totalAmount - ($request->initial_pay ?? $transaction->initial_pay)
        ]);

        return response()->json($transaction);
    }

    public function exportTransactions($status = 'all')
    {
        $query = Transaction::query();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $transactions = $query->get();
        
        $filename = 'transactions_' . $status . '_' . now()->format('Ymd_His') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($file, [
                'ID',
                'Transaction ID',
                'Transaction Name',
                'Client Name',
                'Client Email',
                'Client Mobile',
                'Quantity',
                'Unit Price',
                'Total Amount',
                'Initial Pay',
                'Balance',
                'Status',
                'Created At'
            ]);

            // CSV Data Rows
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->transactionId,
                    $transaction->transactionName,
                    $transaction->clientName,
                    $transaction->clientEmail,
                    $transaction->clientMobile,
                    $transaction->quantity,
                    $transaction->unitPrice,
                    $transaction->totalAmount,
                    $transaction->initial_pay,
                    $transaction->balance,
                    $transaction->status,
                    $transaction->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadTransactionPDF($id)
    {
        $transaction = Transaction::findOrFail($id);
        $filename = 'transaction_receipt_' . $transaction->transactionId . '.pdf';
    
        // Ensure the logo path is correct
        $logoPath = storage_path('app/public/assets/marz-logo.png');
        
        $pdf = Pdf::loadView('transactions.pdf', [
                'transaction' => $transaction,
                'logoExists' => file_exists($logoPath)
            ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'enable_php' => true,
                'chroot' => realpath(base_path()),
            ]);
        
        return $pdf->download($filename);
    }

    public function edit(Transaction $transaction)
    {
        return response()->json($transaction);
    }

    public function updateTransactions(Request $request, Transaction $transaction)
    {
        $request->validate([
            'transactionName' => 'required|string',
            'clientName' => 'required|string',
            'clientEmail' => 'required|email',
            'clientMobile' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'unitPrice' => 'required|numeric|min:0',
            'initial_pay' => 'nullable|numeric|min:0|max:'.$transaction->totalAmount,
            'status' => 'required|in:Paid,Pending,Partially_Paid',
        ]);

        $data = $request->all();
        
        // Recalculate total amount if quantity or unit price changes
        if ($request->has('quantity') || $request->has('unitPrice')) {
            $data['totalAmount'] = $data['quantity'] * $data['unitPrice'];
        }
        
        // Recalculate balance if initial pay or total amount changes
        if ($request->has('initial_pay') || $request->has('totalAmount')) {
            $data['balance'] = $data['totalAmount'] - $data['initial_pay'];
        }

        $transaction->update($data);

        return response()->json($transaction);
    }

    // In your Laravel controller
    public function getTransactionReports(Request $request)
    {
        $query = Transaction::query();
        
        // Apply date filters if provided
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date, 
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }
        
        $transactions = $query->get();
        
        // Calculate summary data
        $reportData = [
            'totalRevenue' => $transactions->sum('totalAmount'),
            'totalPaid' => $transactions->where('status', 'Paid')->sum('totalAmount'),
            'totalPending' => $transactions->where('status', 'Pending')->sum('totalAmount'),
            'totalPartiallyPaid' => $transactions->where('status', 'Partially_Paid')->sum('totalAmount'),
            'totalOutstanding' => $transactions->sum('balance'),
            'detailedTransactions' => $transactions->map(function($transaction) {
                return [
                    'date' => $transaction->created_at->format('Y-m-d'),
                    'transactionId' => $transaction->transactionId,
                    'clientName' => $transaction->clientName,
                    'amount' => $transaction->totalAmount,
                    'paid' => $transaction->initial_pay,
                    'balance' => $transaction->balance,
                    'status' => $transaction->status
                ];
            }),
            'monthlyComparison' => $this->calculateMonthlyComparison($transactions)
        ];
        
        return response()->json($reportData);
    }

    private function calculateMonthlyComparison($transactions)
    {
        $grouped = $transactions->groupBy(function($item) {
            return $item->created_at->format('Y-m');
        });
        
        return $grouped->map(function($monthTransactions, $month) {
            return [
                'month' => $month,
                'revenue' => 'ZKW' . number_format($monthTransactions->sum('totalAmount'), 2),
                'paid' => 'ZKW' . number_format($monthTransactions->sum('initial_pay'), 2),
                'outstanding' => 'ZKW' . number_format($monthTransactions->sum('balance'), 2),
                'profitLoss' => number_format(
                    $monthTransactions->sum('totalAmount') - $monthTransactions->sum('balance'), 
                    2
                )
            ];
        })->values()->toArray();
    }

    // public function exportReport(Request $request)
    // {
    //     // Similar logic to getTransactionReports but export to Excel/PDF
    //     // You can use Laravel Excel package for this
    // }

    public function exportReport(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    
    $transactions =Transaction::query();
    
    if ($startDate && $endDate) {
        $transactions->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
    }
    
    $data = [
        'transactions' => $transactions->get(),
        'startDate' => $startDate,
        'endDate' => $endDate
    ];
    
    $pdf = PDF::loadView('exports.report', $data);
    
    return $pdf->download('report_' . now()->format('Ymd_His') . '.pdf');
}
// Add other CRUD methods as needed
}
