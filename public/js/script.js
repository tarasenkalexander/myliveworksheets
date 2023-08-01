const fileSelector = document.getElementById('file-selector');
const labelSelector = document.getElementById('label-selector');

const output = document.getElementById("output");

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

    reader.readAsDataURL(file);

    const selectorBlock = document.getElementById('selectorBlock');
    selectorBlock.style.display = "none";
    const imageContainer = document.getElementById('imageContainer');
    imageContainer.style.display = "flex";

    const sidepanelFirst = document.getElementById('sidepanelFirst');
    sidepanelFirst.style.display = "flex";
}

const inputDraggable = document.getElementById('input-draggable');
var createdInput;
inputDraggable.addEventListener('dragstart', (event) => {
    event.preventDefault();
    createdInput = inputDraggable.cloneNode();
    createdInput.classList.add('selected');
});

inputDraggable.addEventListener('drag', (event) => {
    createdInput.style.left = event.pageX - createdInput.offsetWidth / 2 + 'px';
    createdInput.style.top = event.pageY - createdInput.offsetHeight / 2 + 'px';
})

inputDraggable.addEventListener('dragend', (event) => {
    alert('dragend');
    event.preventDefault();
    createdInput.classList.remove('selected');
})