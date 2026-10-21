
<?php
// =============================================
// DATABASE CONNECTION
// =============================================
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "birthday_wishnote";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");

$successMessage = "";
$errorMessage = "";

// =============================================
// PROCESS FORM SUBMISSION
// =============================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Capture all four submitted values.
    $FirstName  = trim($_POST["FirstName"] ?? "");
    $LastName   = trim($_POST["LastName"] ?? "");
    $GovPosition = trim($_POST["GovPosition"] ?? "");
    $Location   = trim($_POST["Location"] ?? "");

    // Validate required fields on the server.
    if (
        $FirstName === "" ||
        $LastName === "" ||
        $GovPosition === ""
    ) {
        $errorMessage = "Please complete all required fields.";
    } else {

        /*
         * INTENTIONAL DATABASE INSERTION FLAW:
         * The Location value is captured above but deliberately
         * excluded from both the INSERT column list and bind_param().
         * Therefore, Location is not saved in the database.
         */

        $sql = "INSERT INTO main_corrupt_politicians
                (FirstName, LastName, GovPosition)
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            // Fallback message if the query cannot be prepared.
            $errorMessage = "Error: Unable to prepare the database query.";
        } else {
            $stmt->bind_param(
                "sss",
                $FirstName,
                $LastName,
                $GovPosition
            );

            if ($stmt->execute()) {
                $successMessage =
                    "Success! The wish note has been sent.";
            } else {
                $errorMessage =
                    "Error: The wish note could not be sent.";
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kara David's Wish Note</title>

    <style>
        :root {
            --blue: #2563eb;
            --blue-dark: #1d4ed8;
            --blue-light: #eff6ff;
            --background: #f3f6fb;
            --text: #1e293b;
            --muted: #64748b;
            --border: #dbe3ef;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 32px 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--background);
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
        }

        .card {
            width: 100%;
            max-width: 510px;
            padding: 34px;
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.09);
        }

        .header {
            margin-bottom: 26px;
            text-align: center;
        }

        .header h2 {
            margin: 0 0 10px;
            color: var(--blue);
            font-size: 27px;
        }

        .header p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 19px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .required {
            color: #dc2626;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
            background: #fff;
            color: var(--text);
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .hint {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-size: 12px;
        }

        button {
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        button:active {
            transform: scale(0.98);
        }

        .submit-button {
            width: 100%;
            margin-top: 5px;
            background: var(--blue);
            color: white;
        }

        .submit-button:hover {
            background: var(--blue-dark);
        }

        .message {
            margin-bottom: 22px;
            padding: 14px 16px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
        }

        .success {
            border: 1px solid #a7f3d0;
            background: #ecfdf5;
            color: #065f46;
        }

        .error {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        /* Modal overlay */
        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background: rgba(15, 23, 42, 0.58);
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            width: 100%;
            max-width: 450px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 28px;
            border-radius: 14px;
            background: white;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            animation: appear 0.2s ease-out;
        }

        @keyframes appear {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal h3 {
            margin: 0 0 8px;
            color: var(--blue);
            font-size: 22px;
        }

        .modal-description {
            margin: 0 0 20px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }

        .preview-item {
            margin-bottom: 12px;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #f8fafc;
            overflow-wrap: anywhere;
        }

        .preview-label {
            display: block;
            margin-bottom: 5px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        .preview-value {
            font-size: 14px;
            white-space: pre-wrap;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 22px;
        }

        .cancel-button {
            flex: 1;
            border: 1px solid var(--border);
            background: white;
            color: var(--text);
        }

        .cancel-button:hover {
            background: #f1f5f9;
        }

        .confirm-button {
            flex: 1;
            background: var(--blue);
            color: white;
        }

        .confirm-button:hover {
            background: var(--blue-dark);
        }

        @media (max-width: 480px) {
            body {
                padding: 18px 12px;
            }

            .card {
                padding: 24px 19px;
            }

            .header h2 {
                font-size: 23px;
            }

            .modal {
                padding: 22px 18px;
            }

            .modal-actions {
                flex-direction: column-reverse;
            }

            .modal-actions button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <main class="card">
        <header class="header">
            <h2>Kara David's Wish Note</h2>
            <p>Enter the target's information below and verify it before sending.</p>
        </header>

        <?php if ($successMessage !== ""): ?>
            <div class="message success" role="status">
                <?= htmlspecialchars($successMessage, ENT_QUOTES, "UTF-8") ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage !== ""): ?>
            <div class="message error" role="alert">
                <?= htmlspecialchars($errorMessage, ENT_QUOTES, "UTF-8") ?>
            </div>
        <?php endif; ?>

        <form id="wishForm" method="POST" action="">
            <div class="form-group">
                <label for="FirstName">
                    Target's First Name <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="FirstName"
                    name="FirstName"
                    maxlength="30"
                    required
                    autocomplete="given-name"
                    placeholder="Enter first name"
                    value="<?= htmlspecialchars($_POST["FirstName"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                >
            </div>

            <div class="form-group">
                <label for="LastName">
                    Target's Last Name <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="LastName"
                    name="LastName"
                    maxlength="30"
                    required
                    autocomplete="family-name"
                    placeholder="Enter last name"
                    value="<?= htmlspecialchars($_POST["LastName"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                >
            </div>

            <div class="form-group">
                <label for="GovPosition">
                    Target's Government Position <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="GovPosition"
                    name="GovPosition"
                    maxlength="20"
                    required
                    placeholder="Enter government position"
                    value="<?= htmlspecialchars($_POST["GovPosition"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                >
            </div>

            <div class="form-group">
                <label for="Location">Target's Location</label>
                <input
                    type="text"
                    id="Location"
                    name="Location"
                    maxlength="40"
                    placeholder="Enter location (optional)"
                    value="<?= htmlspecialchars($_POST["Location"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                >
                <span class="hint">Optional — maximum 40 characters.</span>
            </div>

            <button type="submit" class="submit-button">
                Verify and Send
            </button>
        </form>
    </main>

    <!-- Verification modal -->
    <div
        class="modal-overlay"
        id="verifyModal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modalTitle"
        aria-hidden="true"
    >
        <div class="modal">
            <h3 id="modalTitle">Verify Target Data</h3>
            <p class="modal-description">
                Please review all the information below before sending.
            </p>

            <div class="preview-item">
                <span class="preview-label">Target's First Name</span>
                <div class="preview-value" id="previewFirstName"></div>
            </div>

            <div class="preview-item">
                <span class="preview-label">Target's Last Name</span>
                <div class="preview-value" id="previewLastName"></div>
            </div>

            <div class="preview-item">
                <span class="preview-label">Target's Government Position</span>
                <div class="preview-value" id="previewGovPosition"></div>
            </div>

            <div class="preview-item">
                <span class="preview-label">Target's Location</span>
                <div class="preview-value" id="previewLocation"></div>
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-button" id="cancelButton">
                    Cancel
                </button>
                <button type="button" class="confirm-button" id="confirmButton">
                    Confirm &amp; Send
                </button>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById("wishForm");
        const modal = document.getElementById("verifyModal");
        const cancelButton = document.getElementById("cancelButton");
        const confirmButton = document.getElementById("confirmButton");

        // Intercept submission and show the verification modal.
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            // Preserve normal HTML required-field validation.
            if (!form.reportValidity()) {
                return;
            }

            document.getElementById("previewFirstName").textContent =
                document.getElementById("FirstName").value || "(Not provided)";

            document.getElementById("previewLastName").textContent =
                document.getElementById("LastName").value || "(Not provided)";

            document.getElementById("previewGovPosition").textContent =
                document.getElementById("GovPosition").value || "(Not provided)";

            document.getElementById("previewLocation").textContent =
                document.getElementById("Location").value || "(Not provided)";

            modal.classList.add("active");
            modal.setAttribute("aria-hidden", "false");
            cancelButton.focus();
        });

        // Close the modal without submitting the form.
        function closeModal() {
            modal.classList.remove("active");
            modal.setAttribute("aria-hidden", "true");
            document.querySelector(".submit-button").focus();
        }

        cancelButton.addEventListener("click", closeModal);

        // Submit directly, bypassing the submit event interceptor.
        confirmButton.addEventListener("click", function () {
            HTMLFormElement.prototype.submit.call(form);
        });

        // Close when clicking the dark overlay outside the modal.
        modal.addEventListener("click", function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Allow Escape to close the modal.
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape" && modal.classList.contains("active")) {
                closeModal();
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
