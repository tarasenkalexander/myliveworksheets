const fileSelector = document.getElementById('file-selector');
const labelSelector = document.getElementById('label-selector');

const status = document.getElementById("status");
const output = document.getElementById("output");
const progress = document.getElementById("progress");

if (window.FileList && window.File && window.FileReader) {
    fileSelector.addEventListener('change', (event) => {
        const file = event.target.files[0];
        onFilesUpload(file);
    });
}

labelSelector.addEventListener('dragover', (event) => {
    event.stopPropagation();
    event.preventDefault();
    event.dataTransfer.dropEffect = "copy";
});

labelSelector.addEventListener('drop', (event) => {
    event.stopPropagation();
    event.preventDefault();
    const file = event.dataTransfer.files[0];
    onFilesUpload(file);
});

function onFilesUpload(file) {
    output.src = '';
    status.textContent = '';

    if (!file.type) {
        status.textContent = 'Error: The File.type property does not appear to be supported on this browser.';
        return;
    }

    const reader = new FileReader();

    reader.addEventListener('load', event => {
        output.src = event.target.result;
    });

    reader.addEventListener('progress', (event) => {
        if (event.loaded && event.total) {
            const percent = (event.loaded / event.total) * 100;
            progress.textContent = "Progress:" + Math.round(percent);
        }
    });
    reader.readAsDataURL(file);
}