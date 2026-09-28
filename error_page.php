<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - RHS</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .error-container {
            text-align: center;
            max-width: 500px;
            width: 100%;
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: #e2e8f0;
            line-height: 1;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .error-message {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: #ffffff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
            color: #1e293b;
            transform: translateY(-1px);
        }

        @media (max-width: 480px) {
            .btn-group {
                flex-direction: column;
            }
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="error-container">
        <!-- Error Code -->
        <div class="error-code">404</div>
        
        <!-- Error Title -->
        <h1 class="error-title">Oops! Page not found</h1>
        
        <!-- Error Message Description -->
        <p class="error-message">
            We can't seem to find the page you're looking for. It might have been moved, deleted, or perhaps the URL was mistyped.
        </p>
        
        <!-- Action Buttons -->
        <div class="btn-group">
            <a href="index.php" class="btn btn-primary">Go to Homepage</a>
            <button onclick="window.history.back()" class="btn btn-secondary">Go Back</button>
        </div>
    </div>

</body>
</html>