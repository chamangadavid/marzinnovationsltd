<template>
    <div class="loading-screen">
      <div class="loading-content">
        <div class="logo-container">
          <img src="/assets/favivon.png" alt="Marz Innovation Logo" class="logo" />

        </div>
        <h1 class="company-name">Marz Innovations Ltd</h1>
        <a-progress
          :percent="progress"
          :stroke-color="['#7e22ce', '#a855f7']"
          :show-info="false"
          stroke-linecap="round"
        />
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      progress: {
        type: Number,
        default: 0
      }
    }
  }
  </script>
  
  <style scoped>
  .loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }
  
  .loading-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2rem;
    width: 100%;
    max-width: 400px;
    padding: 2rem;
  }
  
  .logo-container {
    width: 120px;
    height: 120px;
  }
  
  .logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
    animation: pulse 2s infinite;
  }
  
  .company-name {
    color: #4B5563;
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
  }
  
  @keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
  }
  </style>