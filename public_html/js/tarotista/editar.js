const btnRemove = document.getElementById("remove-photo-btn");
const inputRemove = document.getElementById("remove_image");
const imgEl = document.querySelector(".preview-img");
const fileEl = document.getElementById("image");


btnRemove.addEventListener("click", (e) => {
    e.preventDefault();
    inputRemove.value = "1";
    fileEl.value = "";
    imgEl.src = "/images/no-person.png";
})

fileEl.addEventListener('change', function() {
    const file = this.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            inputRemove.value = "0";
            imgEl.setAttribute('src', e.target.result);
        }
        reader.readAsDataURL(file); 
    }
});