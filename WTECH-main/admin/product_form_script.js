document.getElementById("add-image-btn").addEventListener("click", function() {
    let uploadContainer = document.getElementById("upload-boxes");
    let newUploadBox = document.createElement("div");
    newUploadBox.classList.add("upload-box");
    newUploadBox.innerHTML = "Upload File <br> (JPG, PNG, PDF)";
    uploadContainer.appendChild(newUploadBox);
});

