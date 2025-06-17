<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Email Verification</title>
</head>
<body>
  <h1>Email Verification</h1>
  <div id="message">Verifying...</div>

  <script>
    const params = new URLSearchParams(window.location.search);
    const email = params.get("email");
    const code = params.get("code");

    const formData = new FormData();
    formData.append("email", email);
    formData.append("code", code);

    const messageDiv = document.getElementById("message");

    if (email && code) {
      fetch("verify_email.php", {
        method: "POST",
        body: formData 
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          messageDiv.textContent = "Verification successful!";
        } else {
          messageDiv.textContent = "Verification failed: " + (data.message || "Invalid code.");
        }
      })
      .catch(err => {
        messageDiv.textContent = "Error contacting server.";
        console.error(err);
      });
    } else {
      messageDiv.textContent = "Missing email or code in URL.";
    }
  </script>
</body>
</html>