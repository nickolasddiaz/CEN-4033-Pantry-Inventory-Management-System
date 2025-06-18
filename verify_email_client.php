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
        // Get the raw query string and parse it manually to preserve + in email addresses
        const queryString = window.location.search.substring(1);
        const urlParams = {};
        
        if (queryString) {
            queryString.split('&').forEach(param => {
                const [key, value] = param.split('=');
                if (key && value) {
                    // Only decode %XX sequences, but preserve + characters
                    urlParams[decodeURIComponent(key)] = decodeURIComponent(value);
                }
            });
        }
        
        const email = urlParams.email;
        const code = urlParams.code;
        
        const messageDiv = document.getElementById("message");
        
        if (email && code) {
            // URLSearchParams.get() automatically decodes URL-encoded characters
            // So email will be properly decoded (+ becomes + instead of space)
            
            const formData = new FormData();
            formData.append("email", email);
            formData.append("code", code);
            
            // Debug: Show what we're sending
            console.log("Email:", email);
            console.log("Code:", code);
            
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
                console.error("Fetch error:", err);
            });
        } else {
            messageDiv.textContent = "Missing email or code in URL.";
            console.log("Missing parameters - Email:", email, "Code:", code);
        }
    </script>
</body>
</html>
