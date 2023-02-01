let getUserListRequest = new XMLHttpRequest();

let userType = window.location.href.slice(window.location.href.lastIndexOf("/")+1).split("#")[0];

initializationBtnAdd();
initializationExcelBtnAdd();

/* Проверка вида типа поьзователя (студент, учитель, администрация) */

if(userType == "teacher") {
	getUserListRequest.open("GET", `/admin/user/teacher/null`, true);
	getUserListRequest.send();
} else if(userType == "administrator") {
	getUserListRequest.open("GET", `/admin/user/administrator/null`, true);
	getUserListRequest.send();
} else if(userType == "student") {
	let groupSelect = document.querySelector('select[name="group"]');

	groupSelect.addEventListener('input', () => {
		getUserListRequest.open("GET", `/admin/user/student/${groupSelect.value}`, true);
		getUserListRequest.send();
	});
}

let userList, groupName, groupList;

let userRows = {}; 

let isSelectList = [];

getUserListRequest.onreadystatechange = function() {
	let tableBlock = document.querySelector(".admin__table");
	if(!document.querySelector('.lds-ring')) {
		tableBlock.innerHTML = `<div class="lds-ring"><div></div><div></div><div></div><div></div></div>`
	}

	if(this.readyState == 4 && this.status == 200) {
		if(userType == "teacher") {
			[userList, groupList] = JSON.parse(this.response);
		} else if(userType == "administrator") {
			userList = JSON.parse(this.response);
		} else if(userType == "student") {
			[userList, groupName, groupList] = JSON.parse(this.response);
			groupName = groupName;
		}

		userRows = {};

		console.log(userList);
		console.log(groupName);
		console.log(groupList);

		for(user in userList) {
			userRows[user] = userList[user];
			if(userType == "student") userRows[user]['group'] = groupName[1];
			
		}

		

		if(userType != "administrator") {
			let groups = groupList; 
			groupList = {};
			groups.forEach((group) => groupList[group['id']] = group['name']);
		}

		let table = document.querySelector('.admin__table');

		table.innerHTML = createTable(userRows, groupList);
		addEvents();

	}
}

/*Функция формирования списка пользователей */

function createTable(userRows, groupList) {
	let table = `<table>`;

	for(user in userRows) {
		if(isSelectList.includes(user)) {
			if(groupList) {
				table += `<tr class="tr__edit" location="${user}">
							<td><input type="text" name="surname" placeholder="Фамилия" value="${userRows[user]['surname']}"></td>
							<td><input type="text" name="name" placeholder="Имя" value="${userRows[user]['name']}"></td>
							<td><input type="text" name="patronymic" placeholder="Отчество" value="${userRows[user]['patronymic']}"></td>
							<td><input type="text" name="login" placeholder="Логин" value="${userRows[user]['login']}"></td>
							<td><input type="text" name="password" placeholder="Пароль" value="${userRows[user]['password']}"></td>
							<td>
								${getGroupSelect(groupList, userRows[user]['group'])}
							</td>
							<td>
								<div class="td__edit">
									<div class="admin__btn btn__edit" location="${user}">
										<svg>
											<use xlink:href="#ok"></use>
										</svg>
									</div>
								</div>
							</td>
						</tr>`
			} else {
				table += `<tr class="tr__edit" location="${user}">
							<td><input type="text" name="surname" placeholder="Фамилия" value="${userRows[user]['surname']}"></td>
							<td><input type="text" name="name" placeholder="Имя" value="${userRows[user]['name']}"></td>
							<td><input type="text" name="patronymic" placeholder="Отчество" value="${userRows[user]['patronymic']}"></td>
							<td><input type="text" name="login" placeholder="Логин" value="${userRows[user]['login']}"></td>
							<td><input type="text" name="password" placeholder="Пароль" value="${userRows[user]['password']}"></td>
							<td>
								<div class="td__edit">
									<div class="admin__btn btn__edit" location="${user}">
										<svg>
											<use xlink:href="#ok"></use>
										</svg>
									</div>
								</div>
							</td>
						</tr>`
			}
		} else {
			table += `<tr location="${user}">
					<td>${userRows[user]['surname']}</td>
					<td>${userRows[user]['name']}</td>
					<td>${userRows[user]['patronymic']}</td>
					<td>${userRows[user]['login']}</td>
					<td>${userRows[user]['password'].replace(/./g, '*')}</td>`;
					if(userType != "administrator") {
						table += `<td>${groupList[userRows[user]['group']] ? groupList[userRows[user]['group']] : "-"}</td>`;
					}
					table +=`<td>
						<div class="td__btn">
							<input type="checkbox" name="" class="admin__choose" location="${user}">
							<div class="admin__btn btn__upd" location="${user}">
								<svg>
									<use xlink:href="#pencil"></use>
								</svg>
							</div>
							<div class="admin__btn btn__del" location="${user}">
								<svg>
									<use xlink:href="#cross"></use>
								</svg>
							</div>
						</div>	
					</td>
			    </tr>`;
		}
		
	}

	table += getDownPanel(groupList);

	table += '</table>';

	return table;
}

/* Формирование нижней панели */

function getDownPanel(groupList) {
	if(groupList) {
		return `<tr class="tr__add">
				<td><input type="text" name="surname" placeholder="Фамилия"></td>
				<td><input type="text" name="name" placeholder="Имя"></td>
				<td><input type="text" name="patronymic" placeholder="Отчество"></td>
				<td><input type="text" name="login" placeholder="Логин"></td>
				<td><input type="text" name="password" placeholder="Пароль"></td>
				<td>
					${getGroupSelect(groupList)}
				</td>
				<td>
					<div class="td__add">
						<div class="admin__btn btn__add">
							<svg>
								<use xlink:href="#add"></use>
							</svg>
						</div>
					</div>
				</td>
			</tr>
			<tr class="add__objects"> 
				<td colspan="6">Работа над несколькими объектами:</td>
				<td>
					<div class="td__btn">
						<div class="admin__btn btn__add">
							<label for="file">
								<svg>
									<use xlink:href="#add"></use>
								</svg>
							</label>
						</div>
						<div class="admin__btn btn__upd">
							<svg>
								<use xlink:href="#pencil"></use>
							</svg>
						</div>
						<div class="admin__btn btn__del">
							<svg>
								<use xlink:href="#cross"></use>
							</svg>
						</div>
					</div>
				</td>
			</tr>`
	} 

	return `<tr class="tr__add">
				<td><input type="text" name="surname" placeholder="Фамилия"></td>
				<td><input type="text" name="name" placeholder="Имя"></td>
				<td><input type="text" name="patronymic" placeholder="Отчество"></td>
				<td><input type="text" name="login" placeholder="Логин"></td>
				<td><input type="text" name="password" placeholder="Пароль"></td>
				<td>
					<div class="td__add">
						<div class="admin__btn btn__add">
							<svg>
								<use xlink:href="#add"></use>
							</svg>
						</div>
					</div>
				</td>
			</tr>
			<tr class="add__objects"> 
				<td colspan="${userType != "administrator" ? 6 : 5}">Работа над несколькими объектами:</td>
				<td>
					<div class="td__btn">
						<div class="admin__btn btn__add">
							<label for="file">
								<svg>
									<use xlink:href="#add"></use>
								</svg>
							</label>
						</div>
						<div class="admin__btn btn__upd">
							<svg>
								<use xlink:href="#pencil"></use>
							</svg>
						</div>
						<div class="admin__btn btn__del">
							<svg>
								<use xlink:href="#cross"></use>
							</svg>
						</div>
					</div>
				</td>
			</tr>`
}

function getGroupSelect(groupList, selectId) {
	let select = `<div class="select__td">
					<select>`;

	if(!selectId) select += `<option disabled selected>Группа</option>`

	for(group in groupList) {
		console.log(group, selectId)
		if(selectId == group) {
			select += `<option value="${group}" selected>${groupList[group]}</option>`
		} else {
			select += `<option value="${group}">${groupList[group]}</option>`
		}
		
	}

	select += `	</select>	
				<div>
					<svg class="select__btn">
						<use xlink:href="#arrow"></use>
					</svg>
				</div>
			</div>`;

	return select;
}

/* Добавление обработичков событий на кнопки и формы */

function addEvents() {
	let isSelectNodeList = document.querySelectorAll(".admin__choose");

	for(let i = 0; i < isSelectNodeList.length; i++) {
		isSelectNodeList[i].addEventListener('click', () => {
			if(!isSelectNodeList[i].checked) {
				isSelectList = isSelectList.filter(function(item) {
					return item !== isSelectNodeList[i].getAttribute('location');
				}) 
			} else {
				isSelectList.push(isSelectNodeList[i].getAttribute('location'));
			}
			console.log(isSelectList);
		})
	}

	let delBtnList = document.querySelectorAll(".btn__del");

	for(let i = 0; i < delBtnList.length-1; i++) {
		delBtnList[i].addEventListener('click', () => {
			let userId = delBtnList[i].getAttribute('location');

			let userDeleteRequest = new XMLHttpRequest();
			userDeleteRequest.open("POST", `/admin/delUser`);
			userDeleteRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			userDeleteRequest.send(`data=${JSON.stringify([[userId], userType])}`);

			console.log(`Удалить пользователя - ${userId}`);
			// перерисовать таблицу
			delete userRows[userId];

			let table = document.querySelector('.admin__table');
			table.innerHTML = createTable(userRows, groupList);
			addEvents();
		});
	}

	delBtnList[delBtnList.length-1].addEventListener('click', () => {
		let userDeleteRequest = new XMLHttpRequest();
		userDeleteRequest.open("POST", `/admin/delUser`);
		userDeleteRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		userDeleteRequest.send(`data=${JSON.stringify([isSelectList, userType])}`);

		console.log(`Удалить пользователя - ${isSelectList}`);
		
		// перерисовать таблицу
		for(userId in isSelectList) {
			delete userRows[isSelectList[userId]];
		}

		let table = document.querySelector('.admin__table');
		table.innerHTML = createTable(userRows, groupList);
		addEvents();

		isSelectList = [];
	});

	initializationBtnAdd();
	initializationExcelBtnAdd();

	let editBtnList = document.querySelectorAll('.btn__upd');

	for(let i = 0; i < editBtnList.length; i++) {
		editBtnList[i].addEventListener('click', () => {
			let userId = editBtnList[i].getAttribute('location');
			if(userId) isSelectList = [userId];
			let table = document.querySelector('.admin__table');
			table.innerHTML = createTable(userRows, groupList);
			addEvents();
		});
	}

	let btnEditList = document.querySelectorAll('.btn__edit');

	for(let i = 0; i < btnEditList.length; i++) {
		btnEditList[i].addEventListener('click', () => {
			let userId = btnEditList[i].getAttribute('location');

			let inputList = document.querySelectorAll(`.tr__edit[location="${userId}"] input`);
			let select = document.querySelector(`.tr__edit[location="${userId}"] select`);

			let flag = true;

			for(let i = 0; i < inputList.length; i++) {
				flag = flag && inputList[i].value;
			}

			if(userType == "student") flag = flag && select.value != "Группа";

			if(flag) {
				let inputData = {}
				inputData['id'] = userId;
				inputList.forEach((elem) => inputData[elem.name] = elem.value);
				if(userType != "administrator") inputData["group"] = select.value;

				console.log(inputData)

				updateUserRequest = new XMLHttpRequest();
				updateUserRequest.open("POST", `/admin/updUser`);
				updateUserRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				updateUserRequest.send(`data=${JSON.stringify([inputData, userType])}`);

				isSelectList = isSelectList.filter(function(item) {
					return item != userId;
				})

				if(userType == "student") {
					if(inputData['group'] == groupName) {
						userRows[userId] = inputData;
					} else {
						delete userRows[userId];
					}
					
				} else {
					userRows[userId] = inputData;
				}

				
				let table = document.querySelector('.admin__table');
				table.innerHTML = createTable(userRows, groupList);
				addEvents();
			}

		})
	}
}

function initializationBtnAdd() {
	let btnAdd = document.querySelector('.td__add .btn__add');

	btnAdd.addEventListener('click', () => {
		let inputList = document.querySelectorAll(".tr__add input");
		let select = document.querySelector(".tr__add select");

		let flag = true;

		for(let i = 0; i < inputList.length; i++) {
			flag = flag && inputList[i].value;
		}

		if(userType == "student") flag = flag && select.value != "Группа";

		if(flag) {
			let inputData = {}
			inputList.forEach((elem) => inputData[elem.name] = elem.value);
			if(userType != "administrator") inputData["group"] = select.value;

			console.log(inputData);

			addUserRequest = new XMLHttpRequest();
			addUserRequest.open("POST", `/admin/addUser`);
			addUserRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			addUserRequest.send(`data=${JSON.stringify([inputData, userType])}`);

			addUserRequest.onreadystatechange = function() {
				if(this.readyState == 4 && this.status == 200) {
					let userId = this.response;
					userRows[userId] = inputData;
					if(userRows[userId]['group'] == "Группа") {
						delete userRows[userId]['group'];
					}
					if(userType == "student") {
						if(groupName[1] == select.value) {
							let table = document.querySelector('.admin__table');
							table.innerHTML = createTable(userRows, groupList);
							addEvents();
						}
					} else {
						let table = document.querySelector('.admin__table');
						table.innerHTML = createTable(userRows, groupList);
						addEvents();
					}
				}
			}
		}
	})
}

function initializationExcelBtnAdd() {
	let btnAdd = document.querySelector("input[type='file']");

	btnAdd.addEventListener('input', () => {
		document.querySelector('.fileWrapper').className = "fileWrapper visible";
		document.querySelector('.fileBlock').className = "fileBlock visible";
		document.querySelector('.fileBlock__filename').innerHTML = btnAdd.value.slice(btnAdd.value.lastIndexOf("\\")+1);
		document.querySelector(".fileBlock__close").addEventListener("click", () => {
			document.querySelector('.fileWrapper').className = "fileWrapper hidden";
			document.querySelector('.fileBlock').className = "fileBlock hidden";
		});
	});
}

