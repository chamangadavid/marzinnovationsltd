<template>
  <div class="col-md-2">
    <div class="certificate-card">
      <div class="header">
        <img
          src="https://www.peoplecert.org/-/media/peoplecert/images/logos/axelos-logos/itil.png?h=160&w=449&hash=54A47EC9F0653C0B6EF390780FB167EE"
          alt="ITIL Logo"
          class="logo"
        />
        <span class="status active">ACTIVE</span>
      </div>
      <div class="content">
        <p class="certificate-number">Certificate Number</p>
        <p class="certificate-id">GR750390196WM</p>
        <p class="issue-date">ISSUE 13/03/2018</p>
        <h2 class="certificate-title">
          ITIL<sup>®</sup> Foundation Certificate in IT Service Management
        </h2>
        <a href="your-certificate-link" class="view-certificate">View Certificate</a>
        <a href="your-hardcopy-link" class="get-hard-copy">Get hard copy</a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CertificateCard",
};
</script>

<style scoped>
.certificate-card {
  width: auto;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 11px 30px 0 rgba(0, 0, 0, 0.07);
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.logo {
  height: 30px;
}

.status {
  font-size: 12px;
  color: #fff;
  padding: 5px 10px;
  border-radius: 4px;
}

.status.active {
  background-color: #4caf50;
}

.content {
  text-align: left;
}

.certificate-number,
.certificate-id,
.issue-date {
  margin: 5px 0;
  font-size: 14px;
  color: #666;
}

.certificate-title {
  font-size: 18px;
  margin: 10px 0;
  color: #333;
  font-size: 1.4em;
  font-weight: 700;
  line-height: 22.69px;
}

.certificate-title sup {
  font-size: 12px;
}

.view-certificate,
.get-hard-copy {
  display: block;
  font-size: 14px;
  color: #d32f2f;
  text-decoration: none;
  margin: 10px 0;
}

.view-certificate:hover,
.get-hard-copy:hover {
  text-decoration: underline;
}
</style>
