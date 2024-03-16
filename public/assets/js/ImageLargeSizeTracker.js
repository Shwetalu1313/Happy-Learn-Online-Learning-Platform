window.addEventListener('DOMContentLoaded', (event) => {
    makeBase64ImagesResponsive();
});

function makeBase64ImagesResponsive() {
    const images = document.querySelectorAll('img[src^="data:image/"]');

    images.forEach(function(img) {
        // Create a new Image object to handle the Base64 data
        const blobImage = new Image();
        blobImage.onload = function() {
            // Get image natural width and height
            const imageNaturalWidth = blobImage.naturalWidth;
            const imageNaturalHeight = blobImage.naturalHeight;

            // Get screen width and height
            const screenWidth = window.innerWidth;
            const screenHeight = window.innerHeight;

            // Check if image is larger than screen size
            if (imageNaturalWidth > screenWidth || imageNaturalHeight > screenHeight) {
                // Make image responsive
                img.style.maxWidth = '80%';
                img.style.height = 'auto';

                // You can also add additional tracking code here
                console.log('Image is larger than screen size.');
            } else {
                console.log('Image is not larger than screen size.');
            }
        };
        // Assign the Base64 URL to the Image object
        blobImage.src = img.src;
    });
}
