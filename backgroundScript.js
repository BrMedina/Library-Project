const backgroundImages = [
    "./assets/background/bg-2.jpg",
    "./assets/background/bg-5.jpg",
    "./assets/background/bg-7.jpg",
    "./assets/background/bg-8.jpg",
    "./assets/background/bg-9.jpg"
];

let backgroundIndex = 0;

function setBackgroundImage() {
    document.body.style.backgroundImage = `url(${backgroundImages[backgroundIndex]})`;
    backgroundIndex = (backgroundIndex + 1) % backgroundImages.length;
}

backgroundImages.forEach((src) => {
    const img = new Image();
    img.src = src;
});

setBackgroundImage();
setInterval(setBackgroundImage, 10000);


function previewing(event) {
    var displaying = document.getElementById('preview');
    console.log(displaying);
    displaying.src = URL.createObjectURL(event.target.files[0]);
}
