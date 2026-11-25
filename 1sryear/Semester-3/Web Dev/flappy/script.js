const lockScreen = document.getElementById("lockScreen");
const homeScreen = document.getElementById("homeScreen");

const timeTop = document.getElementById("timeTop");
const timeMain = document.getElementById("timeMain");
const timeHome = document.getElementById("timeHome");
const dateMain = document.getElementById("dateMain");

// Update time
function updateTime() {
    const now = new Date();

    const hours = now.getHours().toString().padStart(2, "0");
    const minutes = now.getMinutes().toString().padStart(2, "0");

    const time = `${hours}:${minutes}`;

    timeTop.textContent = time;
    timeMain.textContent = time;
    timeHome.textContent = time;

    const options = { weekday: "long", month: "long", day: "numeric" };
    dateMain.textContent = now.toLocaleDateString(undefined, options);
}

setInterval(updateTime, 1000);
updateTime();

// Unlock on tap
lockScreen.addEventListener("click", () => {
    lockScreen.classList.remove("active");
    homeScreen.classList.add("active");
});
const appScreen = document.getElementById("appScreen");
const appFrame = document.getElementById("appFrame");
const homeBtn = document.getElementById("homeBtn");
const appTitle = document.getElementById("appTitle");

// Open app
function openApp(name, path) {
    homeScreen.classList.remove("active");
    appScreen.style.display = "flex";
    appFrame.src = path;
    appTitle.textContent = name;
}

// Close app
homeBtn.addEventListener("click", () => {
    appFrame.src = "";
    appScreen.style.display = "none";
    homeScreen.classList.add("active");
});
