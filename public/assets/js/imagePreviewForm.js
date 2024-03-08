function imagePreview(event) {
    event.preventDefault();
    const preview = document.getElementById('image-preview');
    const file = event.target.files[0];
    const reader = new FileReader();
    preview.style.maxWidth = '150px';
    preview.style.maxHeight = '150px';
    preview.style.marginTop = '20px';

    reader.onload = () => {
        const img = new Image();
        img.src = reader.result;
        img.style.maxWidth = "100%";
        img.style.maxHeight = "100%";

        preview.innerHTML = '';
        preview.appendChild(img);
    }

    if (file) {
        reader.readAsDataURL(file);
    }
}

function resetImage() {
    const input = document.getElementById('image-input');
    input.value = '';
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
}
