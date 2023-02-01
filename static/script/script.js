let menu = document.querySelector('.menu');
let arrowList = document.querySelector('.arrow');
let menuItems = document.querySelector('.menu__list');
let menuStatus = true;

function openMenu() {
	arrowList.style.transform = "rotate(180deg) translate(-10px)";
	menuItems.style.display = "block";
}

function closeMenu() {
	arrowList.style.transform = "rotate(0) translate(0)";
	menuItems.style.display = "none";
}

document.addEventListener('click', (event) => {
	if(event.target.className != "menu__title" && event.target.className.baseVal != "arrow") {
		arrowList.style.transform = "rotate(0) translate(0)";
		menuItems.style.display = "none";
		menuStatus = true;
	}
})

menu.addEventListener('click', () => {
	if(menuStatus) {
		openMenu();
		menuStatus = false;
	} else {
		closeMenu();
		menuStatus = true;
	}
});
