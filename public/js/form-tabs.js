// Event Form Tab Navigation
document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll(".tab-button");
    const tabContents = document.querySelectorAll(".tab-content");
    const prevBtn = document.querySelector(".btn-prev-tab");
    const nextBtn = document.querySelector(".btn-next-tab");
    const progressFill = document.querySelector(".progress-fill");
    const steps = document.querySelectorAll(".step");
    const form = document.getElementById("event-form");

    let currentTab = 0;

    // Initialize tabs
    function showTab(n) {
        // Hide all tabs
        tabContents.forEach((content) => {
            content.classList.remove("active");
        });

        // Remove active class from all tab buttons
        tabs.forEach((tab) => {
            tab.classList.remove("active");
        });

        // Show current tab
        tabContents[n].classList.add("active");
        tabs[n].classList.add("active");

        // Update button visibility
        prevBtn.style.display = n === 0 ? "none" : "inline-flex";
        nextBtn.style.display = n === tabs.length - 1 ? "none" : "inline-flex";

        // Update progress bar
        const progress = ((n + 1) / tabs.length) * 100;
        progressFill.style.width = `${progress}%`;

        // Update steps
        steps.forEach((step, index) => {
            if (index <= n) {
                step.classList.add("active");
            } else {
                step.classList.remove("active");
            }
        });

        currentTab = n;
    }

    // Next button click
    if (nextBtn) {
        nextBtn.addEventListener("click", function () {
            if (validateCurrentTab()) {
                if (currentTab < tabs.length - 1) {
                    showTab(currentTab + 1);
                }
            }
        });
    }

    // Previous button click
    if (prevBtn) {
        prevBtn.addEventListener("click", function () {
            if (currentTab > 0) {
                showTab(currentTab - 1);
            }
        });
    }

    // Tab button click
    tabs.forEach((tab, index) => {
        tab.addEventListener("click", function () {
            if (index <= currentTab || validateCurrentTab()) {
                showTab(index);
            }
        });
    });

    // Validate current tab
    function validateCurrentTab() {
        const currentTabContent = tabContents[currentTab];
        const requiredFields = currentTabContent.querySelectorAll("[required]");

        let isValid = true;

        requiredFields.forEach((field) => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add("error");
            } else {
                field.classList.remove("error");
            }
        });

        return isValid;
    }

    // Image upload preview
    const imageInput = document.getElementById("image");
    const imagePreview = document.getElementById("imagePreview");
    const removeImageBtn = document.getElementById("removeImage");
    const fileInfo = document.getElementById("fileInfo");

    if (imageInput) {
        imageInput.addEventListener("change", function (e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert("File size must be less than 2MB");
                    this.value = "";
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="preview-image">
                        <button type="button" class="remove-image" id="removeImage">
                            <i class="fas fa-times"></i>
                        </button>
                    `;

                    fileInfo.innerHTML = `
                        <p class="file-name">${file.name}</p>
                        <p class="file-size">${(
                            file.size /
                            1024 /
                            1024
                        ).toFixed(2)} MB</p>
                    `;

                    // Re-attach remove button event
                    document
                        .getElementById("removeImage")
                        .addEventListener("click", removeImage);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Remove image function
    function removeImage() {
        if (imageInput) {
            imageInput.value = "";
        }
        imagePreview.innerHTML = `
            <div class="image-placeholder">
                <i class="fas fa-image"></i>
                <span>No image selected</span>
            </div>
        `;
        fileInfo.innerHTML = `
            <p class="file-name">No file chosen</p>
            <p class="file-size">Max size: 2MB</p>
        `;
    }

    if (removeImageBtn) {
        removeImageBtn.addEventListener("click", removeImage);
    }

    // Number input controls
    const numberInputs = document.querySelectorAll(".number-input");

    numberInputs.forEach((input) => {
        const numberInput = input.querySelector("input");
        const upBtn = input.querySelector(".number-up");
        const downBtn = input.querySelector(".number-down");

        if (upBtn && numberInput) {
            upBtn.addEventListener("click", function () {
                numberInput.value = parseInt(numberInput.value || 0) + 1;
            });
        }

        if (downBtn && numberInput) {
            downBtn.addEventListener("click", function () {
                const currentValue = parseInt(numberInput.value || 0);
                if (currentValue > (parseInt(numberInput.min) || 0)) {
                    numberInput.value = currentValue - 1;
                }
            });
        }
    });

    // Event type icons
    const typeSelect = document.getElementById("type");
    const eventIcons = {
        blood_donation: "fa-tint",
        health_camp: "fa-medkit",
        awareness: "fa-bullhorn",
        seminar: "fa-chalkboard-teacher",
        workshop: "fa-users",
        conference: "fa-microphone",
        charity: "fa-hand-holding-heart",
        screening: "fa-stethoscope",
    };

    if (typeSelect) {
        typeSelect.addEventListener("change", function () {
            const selectedOption = this.options[this.selectedIndex];
            const iconClass = eventIcons[this.value] || "fa-calendar-alt";
            this.style.backgroundImage = `none`; // Remove if you want to show icon in select
        });
    }

    // Form submission
    if (form) {
        form.addEventListener("submit", function (e) {
            if (!validateAllTabs()) {
                e.preventDefault();
                alert("Please fill all required fields before submitting.");
                showTab(0);
            }
        });
    }

    function validateAllTabs() {
        let allValid = true;

        tabContents.forEach((content, index) => {
            const requiredFields = content.querySelectorAll("[required]");
            requiredFields.forEach((field) => {
                if (!field.value.trim()) {
                    allValid = false;
                    field.classList.add("error");
                }
            });
        });

        return allValid;
    }

    // Initialize first tab
    showTab(0);
});
