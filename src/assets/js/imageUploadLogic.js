// assets/js/imageUploadLogic.js
document.addEventListener('DOMContentLoaded', () => {
    function setupImagePreview(inputId, previewImgId, placeholderId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewImgId);
        const placeholder = document.getElementById(placeholderId);

        if (input && preview && placeholder) {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                    }
                    reader.readAsDataURL(this.files[0]);
                } else {
                    preview.src = '';
                    preview.style.display = 'none';
                    placeholder.style.display = 'block';
                }
            });
        }
    }

    function setupGalleryPreview(inputId, previewContainerId, placeholderId) {
        const input = document.getElementById(inputId);
        const container = document.getElementById(previewContainerId);
        const placeholder = document.getElementById(placeholderId);

        if (input && container && placeholder) {
            input.addEventListener('change', function() {
                container.innerHTML = '';
                if (this.files && this.files.length > 0) {
                    placeholder.style.display = 'none';
                    
                    // Limit to 6
                    const filesToProcess = Array.from(this.files).slice(0, 6);
                    
                    filesToProcess.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'gallery-preview-item';
                            container.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    });
                } else {
                    placeholder.style.display = 'block';
                }
            });
        }
    }

    setupImagePreview('createCapaInput', 'createCapaPreview', 'createCapaPlaceholder');
    setupGalleryPreview('createGaleriaInput', 'createGaleriaPreview', 'createGaleriaPlaceholder');
    
    setupImagePreview('editCapaInput', 'editCapaPreview', 'editCapaPlaceholder');
    setupGalleryPreview('editGaleriaInput', 'editGaleriaPreview', 'editGaleriaPlaceholder');
});
