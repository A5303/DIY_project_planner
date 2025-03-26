// Show confirmation before deleting a project
function confirmDelete(projectId) {
    if (confirm("❌ Are you sure you want to delete this project?")) {
        window.location.href = "delete_project.php?project_id=" + projectId;
    }
}

// Redirect Functions
function addProject() {
    window.location.href = "add_project.php";
}

function viewProjects() {
    window.location.href = "view_projects.php";
}

function logout() {
    if (confirm("🔒 Are you sure you want to log out?")) {
        window.location.href = "logout.php";
    }
}

// Form Validation for Add/Edit Project Pages
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    if (form) {
        form.addEventListener("submit", function (event) {
            let valid = true;
            const inputs = form.querySelectorAll("input, textarea");

            inputs.forEach((input) => {
                if (input.value.trim() === "") {
                    valid = false;
                    input.style.border = "2px solid red";
                } else {
                    input.style.border = "1px solid #ccc";
                }
            });

            if (!valid) {
                showMessage("⚠️ Please fill in all required fields before submitting.", "error");
                event.preventDefault(); // Prevent form submission if fields are empty
            }
        });

        // Real-time Validation
        form.querySelectorAll("input, textarea").forEach((input) => {
            input.addEventListener("input", function () {
                if (this.value.trim() !== "") {
                    this.style.border = "1px solid #ccc";
                }
            });
        });
    }

    // Display success message after editing a project
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has("message")) {
        showMessage(urlParams.get("message"), "success");
    }
});

// Toggle Favorite Project
function toggleFavorite(projectId, element) {
    let isFavorite = element.classList.contains('active') ? 0 : 1;

    fetch('actions/toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `project_id=${projectId}&is_favorite=${isFavorite}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            element.classList.toggle('active');
        } else {
            showMessage("⚠️ Failed to update favorite status.", "error");
        }
    })
    .catch(() => showMessage("❌ An error occurred. Please try again.", "error"));
}

$(document).ready(function () {
    $("#favoriteBtn").click(function () {
        var projectId = 1; // Replace with dynamic project ID
        $.ajax({
            url: "actions/toggle_favorite.php",
            type: "POST",
            data: { project_id: projectId },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    let btn = $("#favoriteBtn");
                    if (response.favorited) {
                        btn.addClass("favorited").text("Remove from Favorite");
                    } else {
                        btn.removeClass("favorited").text("Add to Favorite");
                    }
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert("An error occurred.");
            }
        });
    });
});

// Display success/error messages in a notification box
function showMessage(message, type) {
    let messageBox = document.createElement("div");
    messageBox.textContent = message;
    messageBox.className = `message-box ${type}`;

    document.body.appendChild(messageBox);

    setTimeout(() => {
        messageBox.style.opacity = "0";
        setTimeout(() => messageBox.remove(), 500);
    }, 3000);
}

// CSS for message styles (Add this in your CSS file or inside a <style> tag in HTML)
/*
.message-box {
    position: fixed;
    top: 15px;
    right: 15px;
    padding: 12px 20px;
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    border-radius: 5px;
    z-index: 1000;
    opacity: 1;
    transition: opacity 0.5s ease-in-out;
}

.message-box.success { background-color: #28a745; } // Green
.message-box.error { background-color: #dc3545; } // Red
*/
