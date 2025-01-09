<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
</head>

<style>
    body {
        background-color: #edf2f7;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    form {
        background-color: white;
        padding: 20px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
    }

    form input[type="email"],
    form input[type="password"],
    form button {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
    }

    form input[type="email"]:focus,
    form input[type="password"]:focus {
        border-color: #3182ce;
        outline: none;
    }

    form button {
        background-color: #edf2f7;
        border: none;
        color: #2d3748;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    form button:hover {
        background-color: #e2e8f0;
        transform: scale(1.02);
    }

    form button:active {
        transform: scale(0.98);
    }

    form button:focus {
        outline: none;
    }
</style>

<body>
    <form method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");

            form.addEventListener("submit", async function(event) {
                event.preventDefault(); // Prevent default form submission

                const token = form.querySelector('input[name="token"]').value;
                const email = form.querySelector('input[name="email"]').value;
                const password = form.querySelector('input[name="password"]').value;
                const passwordConfirmation = form.querySelector('input[name="password_confirmation"]')
                    .value;

                // Prepare the data to send in the API request
                const requestData = {
                    token: token,
                    email: email,
                    password: password,
                    password_confirmation: passwordConfirmation,
                };

                try {
                    // Make the API call
                    const response = await fetch("/api/usermanagement/password/reset", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(requestData),
                    });

                    // Parse the response
                    const data = await response.json();

                    if (response.ok) {
                        alert(data.message || "Password reset successfully!");
                        form.reset(); // Reset the form after successful submission
                    } else {
                        // Handle errors
                        if (data.errors) {
                            let errorMessages = "";
                            for (const key in data.errors) {
                                errorMessages += `${data.errors[key].join(" ")}\n`;
                            }
                            alert(errorMessages);
                        } else {
                            alert(data.message || "An error occurred. Please try again.");
                        }
                    }
                } catch (error) {
                    console.error("Error:", error);
                    alert("Something went wrong. Please try again later.");
                }
            });
        });
    </script>

</body>

</html>
