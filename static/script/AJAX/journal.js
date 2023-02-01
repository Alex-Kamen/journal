let selectList = {
	'chair': false,
	'year': false,
	'discipline': false,
	'group': false,
	'subgroup': false
}

let selectChair = document.querySelector('select[name="chair"]');
let selectYear = document.querySelector('select[name="year"]');
let selectGroup = document.querySelector('select[name="group"]');
let selectDiscipline;

selectChair.addEventListener('input', () => {
	setDisciplineSelect(selectChair.value);
});

selectGroup.addEventListener('input', () => {
	selectList['group'] = true;
	journalRequest(selectDiscipline.value, selectGroup.value, selectYear.value);
});

selectYear.addEventListener('input', () => {
	selectList['year'] = true;
	journalRequest(selectDiscipline.value, selectGroup.value, selectYear.value);
});

function setDisciplineSelect(chairId) {
	let disciplineRequest = new XMLHttpRequest();
	disciplineRequest.open("GET", `/AJAX/disciplineList/${chairId}`, true);
	disciplineRequest.send();

	disciplineRequest.onreadystatechange = function() {
		if(this.readyState == 4 && this.status == 200) {

			let disciplineList = JSON.parse(this.response);
			selectDiscipline = `<option selected disabled value="none">Дисциплина</option>`;
			for(discipline of disciplineList) {
				selectDiscipline += `<option value="${discipline['id']}">
										${discipline['shortName']}
									</option>`;
			}

			selectDiscipline += ``;

			if(document.querySelector('select[name="discipline"]')) {
				document.querySelector('select[name="discipline"]').innerHTML = selectDiscipline;
			} else {
				document.querySelector('form').insertAdjacentHTML("beforeend", `<div class="select">
																					<svg class="select__btn">
																						<use xlink:href="#arrow"></use>
																					</svg>
																					<select name="discipline">
																						${selectDiscipline}
																					</select>
																				</div>
																				<div class="select">
																					<svg class="select__btn">
																						<use xlink:href="#arrow"></use>
																					</svg>
																					<select name="subgroup">
																						<option selected="" disabled="" value="null">Подгруппа</option>
																						<option value="1">1</option>
																						<option value="2">2</option>
																						<option value="3">3</option>
																						<option value="4">4</option>
																						<option value="5">5</option>
																						<option value="null">Без подгруппы</option>
																					</select>
																				</div>`);
			}

			selectDiscipline = document.querySelector('select[name="discipline"]');

			selectDiscipline.addEventListener('input', () => {
				selectList['discipline'] = true;
				journalRequest(selectDiscipline.value, selectGroup.value, selectYear.value);
			});

			selectList['chair'] = true;

			selectSubgroup = document.querySelector('select[name="subgroup"]');

			selectSubgroup.addEventListener('input', () => {
				selectList['subgroup'] = true;
				journalRequest(selectDiscipline.value, selectGroup.value, selectYear.value, selectSubgroup.value);
			});
		}
	}
}

function journalRequest(disciplineId, groupId, yearId, sugroupId = null) {
	//console.log(sugroupId);
	if(selectList['year'] && selectList['discipline'] && selectList['group']) {
		let journal = new XMLHttpRequest();
		journal.open("POST", `/teacher/journalAjax/${disciplineId}/${groupId}/${yearId}`, true);
		journal.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		journal.send(`subgroupId=${sugroupId}`);

		journal.onreadystatechange = function() {
			let tableBlock = document.querySelector(".subtable");
			if(!document.querySelector('.lds-ring')) {
				tableBlock.innerHTML = `<div class="lds-ring"><div></div><div></div><div></div><div></div></div>`
			}
			if(this.readyState == 4 && this.status == 200) {
				setJournal(this.response);
				addEvents();
			}
		};
	}
}


const resultColor = {
	0: 'td__red',
	1: 'td__red',
	2: 'td__red',
	'н/п': 'td__red',
	'п': 'td__yellow',
}

let dataMatrix = [];
let serviceInfo = [];
let data = [];
let totalInfoList = [];
let studentList = [];
let subroupInfo = [];
let studentIdList = [];
const tableBlock = document.querySelector(".subtable");

let resultCellList,
	isFocus,
	menuList,
	resultList;

let activeInputValueButton = false;

function setJournal(response) {
	[data, serviceInfo, totalInfoList, studentList, subroupInfo] = JSON.parse(response);

	dataMatrix = [];

	studentIdList = Object.keys(studentList).sort((idA, idB) => {
		if(studentList[idA]['surname'] > studentList[idB]['surname']) {
			return 1;
		}
		if(studentList[idA]['surname'] < studentList[idB]['surname']) {
			return -1;
		}
		return 0;
	});

	serviceInfoList = Object.keys(serviceInfo).sort((idA, idB) => {
		let dateA = serviceInfo[idA]['date'].split("-");
		let dateB = serviceInfo[idB]['date'].split("-");
		dateA = dateA[1] + "-" + dateA[0];
		dateB = dateB[1] + "-" + dateB[0];
		if(dateA > dateB) {
			return 1;
		}
		if(dateA < dateB) {
			return -1;
		}
		return 0;
	})

	//console.log(studentIdList);

	//console.log(data);
	//console.log(serviceInfo);
	//console.log(studentList);
	//console.log(totalInfoList);
	//console.log(subroupInfo);

	dataMatrix.push([]);
	dataMatrix[0].push(undefined);

	for(let td of serviceInfoList) {
		dataMatrix[0].push(td);
	}
	dataMatrix[0].push('attestation', 'semester', 'exam', 'total');

	for(let tr of studentIdList) {
		dataMatrix.push([tr]);
	}

	//console.log(dataMatrix);

	for(let tr = 1; tr < dataMatrix.length; tr++) {
		for(let td = 1; td < dataMatrix[0].length-4; td++) {
			let studentId = dataMatrix[tr][0];
			let lessonId = dataMatrix[0][td];
			dataMatrix[tr].push(data[studentId] ? data[studentId][lessonId] : undefined);
		}
	}

	for(let tr = 1; tr < dataMatrix.length; tr++) {
		let studentId = dataMatrix[tr][0];
		if(typeof totalInfoList[studentId] != 'undefined') {
			dataMatrix[tr].push(totalInfoList[studentId]['attestation'],
								totalInfoList[studentId]['semester'],
								totalInfoList[studentId]['exam'],
								totalInfoList[studentId]['total']
			);
		}

	}

	console.log(dataMatrix);

	createTable(dataMatrix, serviceInfo, studentList);
	makeStaticTable(dataMatrix);

	initializationButton(tableBlock);
	initializationServiceEvents();
}

const lessonTypes = {
		'Тип':  `<option value="Тип" selected disabled>Тип</option>
				 <option value="defoult" >Обыч</option>
				 <option value="otr">С отр</option>`,
		'defoult':  `<option value="Тип" disabled>Тип</option>
					 <option value="defoult" selected>Обыч</option>
					 <option value="otr">С отр</option>`,
		'otr': `<option value="Тип" disabled>Тип</option>
						 <option value="defoult" >Обыч</option>
						 <option value="otr" selected>C отр</option>`,
}

/* Добавление нового урока */

function addLesson(lessonsCount) {
	let lastId = dataMatrix[0][dataMatrix[0].length-5];
	if(lastId > 0) {
		dataMatrix[0].splice(lessonsCount+1, 0, -1);
		serviceInfo[-1] = {type: "Тип", lessonNumber: "", date: "", theme: ""};
	} else {
		dataMatrix[0].splice(lessonsCount+1, 0, lastId-1);
		serviceInfo[lastId-1] = {type: "Тип", lessonNumber: "", date: "", theme: ""};
	}

	for(let i = 0; i < Object.keys(studentList).length; i++) {
		dataMatrix[i+1].splice(lessonsCount+1, 0, undefined);
	}
	//console.log(dataMatrix);
}

/* Создвание таблицы журнала */

function renderTable(data, serviceInfo, studentList) {

	if(!Object.keys(serviceInfo).length) {
		serviceInfo[-1] = {type: "Тип", lessonNumber: "", date: "", theme: ""};
		dataMatrix[0].splice(data[0].length-4, 0, -1);
		for(let i = 0; i < Object.keys(studentList).length; i++) {
			dataMatrix[i+1].splice(data[0].length-4, 0, undefined);
		}
	}

	let lessonType = 1;

	let trList = [];

	let staticTrList = [];

	staticTrList.push(`
		<tr>
			<td rowspan='3'><div class="td__rotate">Подгруппа</div></td>
			<td>Номер пары</td>
		</tr>`,
	`
		<tr>
			<td>Тип урока</td>
		</tr>`,
	`
		<tr>
			<td>Дата</td>
		</tr>`
	);

	trList.push(`<tr>`, `<tr>`, `<tr>`);

	for(let td = 1; td < data[0].length-4; td++) {
		if(serviceInfo[data[0][td]]['type'] == "otr") {
			lessonType = 2;
		} else {
			lessonType = 1;
		}
		trList[0] += `<td class="td__input" location="tr__0 td__${td}" colspan="${lessonType}">
						  <input type="text" value="${serviceInfo[data[0][td]]['lessonNumber']}" location="tr__0 td__${td}">
					  </td>`;
		trList[1] += `<td colspan="${lessonType}" class="lessonType">
					<div class="select__td">
						<select class="type" location="tr__0 td__${td}">
						${lessonTypes[serviceInfo[data[0][td]]['type']]}
						</select>
						<div>
							<svg class="select__btn">
								<use xlink:href="#arrow"></use>
							</svg>
						</div>
					</div>
				</td>`;
		trList[2] += `<td class="td__input" location="tr__0 td__${td}" colspan="${lessonType}">
						<input type="date" data-date="${serviceInfo[data[0][td]]['date']}" value="${serviceInfo[data[0][td]]['date']}" location="tr__0 td__${td}">
					</td>`;
	}

	trList[0] += `<td rowspan='${Object.keys(studentList).length+4}' class='td__btn'>Создать урок</td>
	<td rowspan='3'><div class="td__rotate">Средний балл</div></td>
	<td rowspan='3'><div class="td__rotate">Аттестация</div></td>
	<td rowspan='3'><div class="td__rotate">Семестр</div></td>
	<td rowspan='3'><div class="td__rotate">Экзамен</div></td>
	<td rowspan='3'><div class="td__rotate">В зачётку</div></td></tr>`;
	trList[1] += `</tr>`;
	trList[2] += `</tr>`;

	for(let tr of studentIdList) {
		let subgroup = ``;
		if(!subroupInfo[tr]) {
			subgroup += `<option value="null" selected>-</option>`;
		} else {
			subgroup += `<option value="null">-</option>`
		}
		for(let i = 1; i < 6; i++) {
			if(subroupInfo[tr] == i) {
				subgroup += `<option value="${i}" selected>${i}</option>`;
			} else {
				subgroup += `<option value="${i}">${i}</option>`;
			}
		}

		staticTrList.push(
			`<tr>
				<td class="subgroup">
					<div class="select__td">
						<select class="type" location="${tr}">
							${subgroup}
						</select>
						<div>
							<svg class="select__btn">
								<use xlink:href="#arrow"></use>
							</svg>
						</div>
					</div>
				</td>
				<td>${studentList[tr]['surname']} ${studentList[tr]['name'][0]}. ${studentList[tr]['patronymic'][0]}.</td>
			</tr>`);
		trList.push(`<tr>`);
	}

	staticTrList.push(`
		<tr>
			<td colspan="2">Тема урока</td>
		</tr>`,
		`
		<tr>
			<td colspan="2">Удалить урок</td>
		</tr>
		`
	);

	let averageMarkList = [];

	for(let tr = 1; tr < data.length; tr++) {
		averageMarkList.push(calculateAverageResult(tr));

		for(let td = 1; td < data[0].length-4; td++) {
			let lessonType = serviceInfo[data[0][td]]['type'];
			if(lessonType == "defoult") {
				trList[tr+2] += `<td class="td__input td__value td__result" location="tr__${tr} td__${td}">
									${getResultColor(data[tr][td] ? data[tr][td][1][0] ? data[tr][td][1][0] : "" : "")}
								 	<div class="td__menu hidden">
										<div class="menu__up">
											<div class="menu__btn">0</div>
											<div class="menu__btn">1</div>
											<div class="menu__btn">2</div>
											<div class="menu__btn">3</div>
											<div class="menu__btn">4</div>
											<div class="menu__btn">5</div>
											<div class="menu__btn">6</div>
											<div class="menu__btn">7</div>
											<div class="menu__btn">8</div>
											<div class="menu__btn">9</div>
											<div class="menu__btn">10</div>
										</div>
										<div class="menu__down">
											<div>
												<div class="menu__btn">п</div>
												<div class="menu__btn menu__credit">зач</div>
												<div class="menu__type">m</div>
												<div class="menu__input">
													<div class="menu__input__value"></div>
												</div>
											</div>
											<div>
												<div class="menu__ok">Ок</div>
												<div class="menu__cancel">Отмена</div>
											</div>
										</div>
									</div>
								 </td>`;
			} else if(lessonType == "otr") {
				trList[tr+2] += `<td class="td__input td__value td__result" location="tr__${tr} td__${td}">
									${getResultColor(data[tr][td] ? data[tr][td][1][0] ? data[tr][td][1][0] : "" : "")}
								 	<div class="td__menu hidden">
										<div class="menu__up">
											<div class="menu__btn">0</div>
											<div class="menu__btn">1</div>
											<div class="menu__btn">2</div>
											<div class="menu__btn">3</div>
											<div class="menu__btn">4</div>
											<div class="menu__btn">5</div>
											<div class="menu__btn">6</div>
											<div class="menu__btn">7</div>
											<div class="menu__btn">8</div>
											<div class="menu__btn">9</div>
											<div class="menu__btn">10</div>
										</div>
										<div class="menu__down">
											<div>
												<div class="menu__btn">п</div>
												<div class="menu__btn menu__credit">зач</div>
												<div class="menu__type">m</div>
												<div class="menu__input">
													<div class="menu__input__value"></div>
												</div>
											</div>
											<div>
												<div class="menu__ok">Ок</div>
												<div class="menu__cancel">Отмена</div>
											</div>
										</div>
									</div>
								 </td>
								 <td class="td__input td__value td__result" location="tr__${tr} td__${td}">
								 	${getResultColor(data[tr][td] ? data[tr][td][1][1] ? data[tr][td][1][1] : "" : "")}
								 	<div class="td__menu hidden">
										<div class="menu__up">
											<div class="menu__btn">0</div>
											<div class="menu__btn">1</div>
											<div class="menu__btn">2</div>
											<div class="menu__btn">3</div>
											<div class="menu__btn">4</div>
											<div class="menu__btn">5</div>
											<div class="menu__btn">6</div>
											<div class="menu__btn">7</div>
											<div class="menu__btn">8</div>
											<div class="menu__btn">9</div>
											<div class="menu__btn">10</div>
										</div>
										<div class="menu__down">
											<div>
												<div class="menu__btn">п</div>
												<div class="menu__btn menu__credit">зач</div>
												<div class="menu__type">m</div>
												<div class="menu__input">
													<div class="menu__input__value"></div>
												</div>
											</div>
											<div>
												<div class="menu__ok">Ок</div>
												<div class="menu__cancel">Отмена</div>
											</div>
										</div>
									</div>
								 </td>`;
			} else if(lessonType == "Тип") {
				trList[tr+2] += `<td class="td__input td__value" location="tr__${tr} td__${td}">
								<span></span>
								<div class="td__menu hidden">
										<div class="menu__up">
											<div class="menu__btn">0</div>
											<div class="menu__btn">1</div>
											<div class="menu__btn">2</div>
											<div class="menu__btn">3</div>
											<div class="menu__btn">4</div>
											<div class="menu__btn">5</div>
											<div class="menu__btn">6</div>
											<div class="menu__btn">7</div>
											<div class="menu__btn">8</div>
											<div class="menu__btn">9</div>
											<div class="menu__btn">10</div>
										</div>
										<div class="menu__down">
											<div>
												<div class="menu__btn">п</div>
												<div class="menu__btn menu__credit">зач</div>
												<div class="menu__type">m/m</div>
												<div class="menu__input">
													<div class="menu__input__value"></div>
													<div class="menu__input__separator">/</div> 
													<div class="menu__input__value"></div>
												</div>
											</div>
											<div>
												<div class="menu__ok">Ок</div>
												<div class="menu__cancel">Отмена</div>
											</div>
										</div>
									</div>
								</td>`;
			}
		}
	}

	for(let tr = 1; tr < data.length; tr++) {
		let td = data[0].length-4;
		trList[tr+2] += `<td class="td__averageMark">${averageMarkList[tr-1]}</td>
						<td class="td__input td__value td__total" location="tr__${tr} td__${td}">
							${getResultColor(data[tr][td] ? data[tr][td] : "")}
							<div class="td__menu hidden">
								<div class="menu__up">
									<div class="menu__btn">0</div>
									<div class="menu__btn">1</div>
									<div class="menu__btn">2</div>
									<div class="menu__btn">3</div>
									<div class="menu__btn">4</div>
									<div class="menu__btn">5</div>
									<div class="menu__btn">6</div>
									<div class="menu__btn">7</div>
									<div class="menu__btn">8</div>
									<div class="menu__btn">9</div>
									<div class="menu__btn">10</div>
								</div>
								<div class="menu__down">
									<div>
										<div class="menu__btn menu__credit">зач</div>
										<div class="menu__btn hidden"></div>
									</div>
									<div>
										<div class="menu__ok">Ок</div>
										<div class="menu__cancel">Отмена</div>
									</div>
								</div>
							</div>
						</td>
						<td class="td__input td__value td__total" location="tr__${tr} td__${td+1}">
							${getResultColor(data[tr][td+1] ? data[tr][td+1] : "")}
							<div class="td__menu hidden">
								<div class="menu__up">
									<div class="menu__btn">0</div>
									<div class="menu__btn">1</div>
									<div class="menu__btn">2</div>
									<div class="menu__btn">3</div>
									<div class="menu__btn">4</div>
									<div class="menu__btn">5</div>
									<div class="menu__btn">6</div>
									<div class="menu__btn">7</div>
									<div class="menu__btn">8</div>
									<div class="menu__btn">9</div>
									<div class="menu__btn">10</div>
								</div>
								<div class="menu__down">
									<div>
										<div class="menu__btn menu__credit">зач</div>
										<div class="menu__btn hidden"></div>
									</div>
									<div>
										<div class="menu__ok">Ок</div>
										<div class="menu__cancel">Отмена</div>
									</div>
								</div>
							</div>
						</td>
						<td class="td__input td__value td__total" location="tr__${tr} td__${td+2}">
							${getResultColor(data[tr][td+2] ? data[tr][td+2] : "")}
							<div class="td__menu hidden">
								<div class="menu__up">
									<div class="menu__btn">0</div>
									<div class="menu__btn">1</div>
									<div class="menu__btn">2</div>
									<div class="menu__btn">3</div>
									<div class="menu__btn">4</div>
									<div class="menu__btn">5</div>
									<div class="menu__btn">6</div>
									<div class="menu__btn">7</div>
									<div class="menu__btn">8</div>
									<div class="menu__btn">9</div>
									<div class="menu__btn">10</div>
								</div>
								<div class="menu__down">
									<div>
										<div class="menu__btn menu__credit">зач</div>
										<div class="menu__btn hidden"></div>
									</div>
									<div>
										<div class="menu__ok">Ок</div>
										<div class="menu__cancel">Отмена</div>
									</div>
								</div>
							</div>
						</td>
						<td class="td__input td__value td__total" location="tr__${tr} td__${td+3}">
							${getResultColor(data[tr][td+3] ? data[tr][td+3] : "")}
							<div class="td__menu hidden">
								<div class="menu__up">
									<div class="menu__btn">0</div>
									<div class="menu__btn">1</div>
									<div class="menu__btn">2</div>
									<div class="menu__btn">3</div>
									<div class="menu__btn">4</div>
									<div class="menu__btn">5</div>
									<div class="menu__btn">6</div>
									<div class="menu__btn">7</div>
									<div class="menu__btn">8</div>
									<div class="menu__btn">9</div>
									<div class="menu__btn">10</div>
								</div>
								<div class="menu__down">
									<div>
										<div class="menu__btn menu__credit">зач</div>
										<div class="menu__btn hidden"></div>
									</div>
									<div>
										<div class="menu__ok">Ок</div>
										<div class="menu__cancel">Отмена</div>
									</div>
								</div>
							</div>
						</td>`
	}

	for(let tr = 1; tr < data.length; tr++) {
		trList[tr+2] += `</tr>`;
	}

	for(let td = 1; td < data[0].length-4; td++) {
		if(serviceInfo[data[0][td]]['type'] == "otr") {
			lessonType = 2;
		} else {
			lessonType = 1;
		}
		trList[trList.length-1] += `<td class="theme" colspan="${lessonType}" location="tr__0 td__${td}">
										<svg class="theme__icon">
											<use xlink:href="#theme" class="theme__icon"></use>
										</svg>
										<div class="theme__block hidden">
											<textarea>${serviceInfo[data[0][td]]['theme']}</textarea>
											<div class="theme__btn"><span>Сохранить</span></div>
										</div>
									</td>`;
	}


	trList[trList.length-1] += `</tr>`;

	trList.push(`<tr>`);

	for(let td = 1; td < data[0].length-4; td++) {
		if(serviceInfo[data[0][td]]['type'] == "otr") {
			lessonType = 2;
		} else {
			lessonType = 1;
		}
		trList[trList.length-1] += `<td colspan="${lessonType}" class="remove">
										<div class="visible lesson__remove">Удалить</div>
										<div class="hidden remove__confirm">
											<div class="td__red">Вы уверены?</div>
											<div>
												<span class="btn__remove" location="${td}">Удалить</span>
												<span class="btn__cansel">Отмена</span>
											</div>
										</div>
									</td>`;
	}


	trList[trList.length-1] += `</tr>`;

	let table = `<div class="table">
					<table>`;
	for(let tr = 0; tr < staticTrList.length; tr++) {
		table += staticTrList[tr];
	}

	table += `		</table>
			</div>
			<div class="table table--journal">
				<table>`;
	////console.log(table);

	for(let tr = 0; tr < trList.length; tr++) {
		table += trList[tr];
	}

	table += `</table>
			</div>`;

	return table;
}

function createTable(data, serviceInfo, studentList) {
	tableBlock.innerHTML = renderTable(data, serviceInfo, studentList);
	const table = document.querySelector('.table--journal');
	table.scrollLeft = table.scrollWidth;
}

/* Измение типа урока */

function changeLessonType(serviceId, type, lessonNumber, date, theme) {
	serviceInfo[serviceId] = {type: type, lessonNumber: lessonNumber, date: date, theme: theme};
	createTable(dataMatrix, serviceInfo, studentList);
	makeStaticTable(dataMatrix);
	addEvents();
	initializationButton(tableBlock);
	initializationServiceEvents();
}


/* Инициализация обработчиков событий */

function initializationButton(tableBlock) {
	let td__btn = document.querySelector(".td__btn");

	td__btn.addEventListener('click', () => {
		addLesson(dataMatrix[0].length-5);
		createTable(dataMatrix, serviceInfo, studentList);
		makeStaticTable(dataMatrix);
		addEvents();
		initializationButton(tableBlock);
		initializationServiceEvents();
	});
}

function initializationServiceEvents() {
	let inputs = document.querySelectorAll('.td__input input[type="text"]');
	let selects = document.querySelectorAll(".lessonType select");
	let dateInputList = document.querySelectorAll('.td__input input[type="date"]');
	let themeList = document.querySelectorAll(".theme textarea");

	for(let i = 0; i < inputs.length; i++) {
		inputs[i].addEventListener('input', () => {
			serviceInfo[dataMatrix[0][i+1]].lessonNumber = inputs[i].value;
			if( selects[i].value != "Тип" &&
				inputs[i].value != "" &&
				dateInputList[i].getAttribute("data-date") != "") {
				updateServiceInfo(dataMatrix[0][i+1], selects[i].value, inputs[i].value, dateInputList[i].getAttribute("data-date"), themeList[i].value);
			} else {
				//console.log(selects[i].value, inputs[i].value, dateInputList[i].value);
			}
		})
	}

	for(let i = 0; i < selects.length; i++) {
		selects[i].addEventListener('input', () => {
			changeLessonType(dataMatrix[0][i+1], selects[i].value, inputs[i].value, dateInputList[i].getAttribute("data-date"), themeList[i].value);
			if( selects[i].value != "Тип" &&
				inputs[i].value != "" &&
				dateInputList[i].getAttribute("data-date") != "") {
				updateServiceInfo(dataMatrix[0][i+1], selects[i].value, inputs[i].value, dateInputList[i].getAttribute("data-date"), themeList[i].value);
			} else {
				//console.log(selects[i].value, inputs[i].value, dateInputList[i].value);
			}
		})
	}

	for(let i = 0; i < dateInputList.length; i++) {
		dateInputList[i].addEventListener('input', () => {
			let dateValue = dateInputList[i].value.split("-");
			dateInputList[i].setAttribute('data-date', `${dateValue[2]}-${dateValue[1]}`);
			serviceInfo[dataMatrix[0][i+1]].date = dateInputList[i].getAttribute("data-date");
			if(selects[i].value != "Тип" &&
				inputs[i].value != "" &&
				dateInputList[i].getAttribute("data-date") != "") {
				updateServiceInfo(dataMatrix[0][i+1], selects[i].value, inputs[i].value, dateInputList[i].getAttribute("data-date"), themeList[i].value);
			}
		});
	}
}

const btn = {
	1 : "1",
	2 : "2",
	3 : "3",
	4 : "4",
	5 : "5",
	6 : "6",
	7 : "7",
	8 : "8",
	9 : "9",
	0 : "0",
	"п" : "п",
	"g" : "п",
	"p" : "зач",
	"з" : "зач",
};

function addEvents() {
	resultCellList = document.querySelectorAll('.td__value');
	menuList = document.querySelectorAll('.td__value .td__menu');
	resultList = document.querySelectorAll('.td__value span');

	isFocus = [];

	resultCellList.forEach(() => {
		isFocus.push(false);
	});

	for(let i = 0; i < resultCellList.length; i++) {
		resultCellList[i].addEventListener('click', () => {
			if(isFocus[i] && activeInputValueButton) return;
			for(let j = 0; j < resultCellList.length; j++) {
				if(isFocus[j]) {
					isFocus[j] = false;
					if(Array.from(resultCellList[j].classList).includes('td__total')) {
						resultCellList[j].className = "td__input td__value td__total";
					} else {
						resultCellList[j].className = "td__input td__value td__result";
					}
					menuList[j].className = "td__menu hidden";
					break;
				}
			}
			isFocus[i] = true;
			activeInputValueButton.className = "menu__input__value";
			activeInputValueButton = false;
			if(Array.from(resultCellList[i].classList).includes('td__total')) {
				resultCellList[i].className = "td__input td__value td__total td__focus";
			} else {
				resultCellList[i].className = "td__input td__value td__result td__focus";
			}

			let [tr, td] = resultCellList[i].getAttribute('location').split(" ");
			tr = tr.slice(4);
			td = td.slice(4);

			if(!['menu__btn', 'menu__ok', 'menu__cancel'].includes(event.target.className) && (dataMatrix[0][td] > 0 || ["attestation", "semester", "exam", "total"].includes(dataMatrix[0][td]))) {
				if(event.target.innerHTML === 'зач') return;
				menuList[i].className = "td__menu visible";
			}

		});
	}

	let menuButtonsList = document.querySelectorAll('.menu__btn');

	for(let i = 0; i < menuButtonsList.length; i++) {
		menuButtonsList[i].addEventListener('click', (event) => {
			let [tr, td] = resultCellList[Math.floor(i/13)].getAttribute('location').split(" ");
			tr = tr.slice(4);
			td = td.slice(4);

			if(activeInputValueButton && (dataMatrix[0][td] > 0 || ["attestation", "semester", "exam", "total"].includes(dataMatrix[0][td]))) {
				if(menuButtonsList[i].innerHTML != "п") {
					activeInputValueButton.innerHTML = menuButtonsList[i].innerHTML;
				}
				return;
			}
			activeInputValueButton.className = "menu__input__value";
			activeInputValueButton = false;

			if(dataMatrix[0][td] > 0 || ["attestation", "semester", "exam", "total"].includes(dataMatrix[0][td])) {
				resultList[Math.floor(i/13)].innerHTML = menuButtonsList[i].innerHTML;
				resultList[Math.floor(i/13)].className = resultColor[menuButtonsList[i].innerHTML]
				menuList[Math.floor(i/13)].className = "td__menu hidden";

				updateResult(td, tr);
			}
		});
	}

	let menuOkButtonList = document.querySelectorAll('.menu__ok');

	for(let i = 0; i < menuOkButtonList.length; i++) {
		menuOkButtonList[i].addEventListener('click', (event) => {
			let [tr, td] = resultCellList[i].getAttribute('location').split(" ");
			tr = tr.slice(4);
			td = td.slice(4);

			// в ячейку значние из td__input
			let valuesNodeList = getInputButtonList(menuList[i], []);
			let valuesList = [];
			if(valuesNodeList.length) {
				if(valuesNodeList[0].innerHTML) menuList[i].className = "td__menu hidden";
				for(let j = 0; j < valuesNodeList.length; j++) {
					valuesList.push(valuesNodeList[j].innerHTML);
				}
			}
			let value = valuesList.join("/");
			if(value && value[value.length-1] != "/" && (dataMatrix[0][td] > 0 || ["attestation", "semester", "exam", "total"].includes(dataMatrix[0][td]))) {
				resultList[i].innerHTML = value;
				resultList[i].className = resultColor[value.split("/")[0]] || resultColor[value.split("/")[1]]
			}
			activeInputValueButton.className = "menu__input__value";
			activeInputValueButton = false;
			menuList[i].className = "td__menu hidden";

			if(dataMatrix[0][td] > 0 || ["attestation", "semester", "exam", "total"].includes(dataMatrix[0][td])) {
				updateResult(td, tr);
			}
		});
	}

	let menuCancelButtonList = document.querySelectorAll('.menu__cancel');

	for(let i = 0; i < menuCancelButtonList.length; i++) {
		menuCancelButtonList[i].addEventListener('click', (event) => {
			let [tr, td] = resultCellList[i].getAttribute('location').split(" ");
			tr = tr.slice(4);
			td = td.slice(4);

			resultList[i].innerHTML = "";
			activeInputValueButton = false;
			menuList[i].className = "td__menu hidden";

			if(dataMatrix[0][td] > 0 || ["attestation", "semester", "exam", "total"].includes(dataMatrix[0][td])) {
				updateResult(td, tr);
			}
		});
	}

	let menuInputButtonList = document.querySelectorAll('.menu__input');
	let menuTypeButtonList = document.querySelectorAll('.menu__type');
	initializationInputValueButton(document.querySelectorAll('.menu__input__value'));

	for(let i = 0; i < menuTypeButtonList.length; i++) {
		menuTypeButtonList[i].addEventListe
		activeInputValueButton.className = "menu__input__value";
		menuTypeButtonList[i].addEventListener('click', () => {
			activeInputValueButton = false;
			if(menuTypeButtonList[i].innerHTML == "m/m") {
				menuTypeButtonList[i].innerHTML = "m";
				menuInputButtonList[i].innerHTML = `<div class="menu__input__value"></div>`;
			} else {
				menuTypeButtonList[i].innerHTML = "m/m";
				menuInputButtonList[i].innerHTML = ` <div class="menu__input__value"></div>
													<div class="menu__input__separator">/</div> 
													<div class="menu__input__value"></div>`
			}
			initializationInputValueButton(document.querySelectorAll('.menu__input__value'));
		})
	}

	let themeCellList = document.querySelectorAll('.theme');
	let themeList = document.querySelectorAll('.theme__block');
	let activeTheme = false;

	for(let i = 0; i < themeCellList.length; i++) {
		themeCellList[i].addEventListener('click', (event) => {
			if(event.target.className == "theme" || event.target.className.baseVal == "theme__icon") {
				for(let j = 0; j < isFocus.length; j++) {
					if(isFocus[j]) {
						disableCell(j);
					}
				}

				let [tr, td] = themeCellList[i].getAttribute('location').split(" ");
				tr = tr.slice(4);
				td = td.slice(4);

				if(dataMatrix[0][td] > 0) {
					if(Array.from(themeList[i].classList).includes('hidden')) {
						if(activeTheme) {
							activeTheme.className = "theme__block hidden";
						}
						themeList[i].className = "theme__block visible";
						activeTheme = themeList[i];
					} else {
						themeList[i].className = "theme__block hidden";
						activeTheme = false;
					}
				}
			}
		});
	}

	let themeButtonList = document.querySelectorAll(".theme__btn");
	let themeTextList = document.querySelectorAll(".theme textarea");
	let inputs = document.querySelectorAll('.td__input input[type="text"]');
	let selects = document.querySelectorAll(".lessonType select");
	let dateInputList = document.querySelectorAll('.td__input input[type="date"]');

	for(let i = 0; i < themeButtonList.length; i++) {
		themeButtonList[i].addEventListener('click', () => {
			let [tr, td] = themeCellList[i].getAttribute('location').split(" ");
			tr = tr.slice(4);
			td = td.slice(4);

			if(dataMatrix[0][td] > 0) {
				themeList[i].className = "theme__block hidden";
				activeTheme = false;
				serviceInfo[dataMatrix[0][i+1]].theme = themeTextList[i].value;
				//console.log(themeTextList[i].value);
				updateServiceInfo(dataMatrix[0][i+1], selects[i].value, inputs[i].value, dateInputList[i].getAttribute("data-date"), themeTextList[i].value);
			}
		})
	}

	setSubgroupsEvents();
	setRemoveLessonEvents();
}

function getInputButtonList(node, result) {
	let childList = node.childNodes;
	for(let i = 0; i < childList.length; i++) {
		if(Array.from(childList[i].classList ?? []).includes("menu__input__value")) {
			result.push(childList[i])
		} else {
			result.concat(getInputButtonList(childList[i], result));
		}
	}
	return result;
}

function initializationInputValueButton(buttonList) {
	buttonList.forEach(elem => {
		elem.addEventListener('click', () => {
			if(elem.className == "menu__input__value td__focus") {
				elem.className = "menu__input__value";
				activeInputValueButton = false;
			} else {
				buttonList.forEach(elem => elem.className = "menu__input__value");
				elem.className = "menu__input__value td__focus";
				activeInputValueButton = elem;
			}
			//console.log(activeInputValueButton)
		});
	});
}

document.addEventListener('keydown', (event) => {
	//console.log(event.key);
	if(activeInputValueButton && btn[event.key.toLowerCase()] && btn[event.key.toLowerCase()] != "п") {
		activeInputValueButton.innerHTML = btn[event.key.toLowerCase()];
		return;
	}

	for(let i = 0; i < resultCellList.length; i++) {
		if(isFocus[i]) {
			let [tr, td] = resultCellList[i].getAttribute('location').split(" ");
			tr = tr.slice(4);
			td = td.slice(4);
			if(btn[event.key.toLowerCase()]) {
				if(dataMatrix[0][td] > 0 || ["attestation", "semester", "exam", "total"].includes(dataMatrix[0][td])) {

					if(["attestation", "semester", "exam", "total"].includes(dataMatrix[0][td]) && btn[event.key.toLowerCase()] == "п") return;

					if(resultList[i].innerHTML == "1" && btn[event.key.toLowerCase()] == "0") {
						resultList[i].innerHTML = 10;
						resultList[i].className = "";
					} else {
						resultList[i].innerHTML = btn[event.key.toLowerCase()];
						resultList[i].className = resultColor[event.key.toLowerCase()];
					}

					//console.log(tr, td);
					updateResult(td, tr);
				}
			} else if(["ArrowUp", "ArrowDown", "ArrowLeft", "ArrowRight"].includes(event.key)) {
				moveFocus(event.key, i);
			} else if(event.key == "Delete") {
				resultList[i].innerHTML = "";
				updateResult(td, tr);
			} else if(event.key == "Enter") {
				let valuesNodeList = getInputButtonList(menuList[i], []);
				let valuesList = [];
				if(valuesNodeList.length) {
					if(valuesNodeList[0].innerHTML) menuList[i].className = "td__menu hidden";
					for(let j = 0; j < valuesNodeList.length; j++) {
						valuesList.push(valuesNodeList[j].innerHTML);
					}
				}
				let value = valuesList.join("/");
				if(value && value[value.length-1] != "/" && dataMatrix[0][td] > 0) {
					resultList[i].innerHTML = value;
					resultList[i].className = resultColor[value.split("/")[0]] || resultColor[value.split("/")[1]]
				}
				activeInputValueButton.className = "menu__input__value";
				activeInputValueButton = false;
				menuList[i].className = "td__menu hidden";

				if(dataMatrix[0][td] > 0 || ["attestation", "semester", "exam", "total"].includes(dataMatrix[0][td])) {
					updateResult(td, tr);
				}
				moveFocus("ArrowDown", i);
			}
			break;
		}
	}
});

function setSubgroupsEvents() {
	let subgroupSelectList = document.querySelectorAll(".subgroup select");
	subgroupSelectList.forEach((node) => {
		node.addEventListener("input", () => {
			let studentId = node.getAttribute('location');
			//console.log(`студент ${studentId} принадлежит подгруппе ${node.value}`);
			updateSubgroupInfo(studentId, node.value, selectDiscipline.value, selectYear.value, selectGroup.value);
		})
	})
}

function disableCell(position) {
	isFocus[position] = false;
	if(Array.from(resultCellList[position].classList).includes('td__total')) {
		resultCellList[position].className = "td__input td__value td__total";
	} else {
		resultCellList[position].className = "td__input td__value td__result";
	}
	menuList[position].className = "td__menu hidden";
	activeInputValueButton = false;
}

function moveFocus(direction, position) {

	switch(direction) {
		case "ArrowUp": {
			if(!resultCellList[position-resultCellList.length/Object.keys(studentList).length]) return;
			disableCell(position)
			if(Array.from(resultCellList[position-resultCellList.length/Object.keys(studentList).length].classList).includes('td__total')) {
				resultCellList[position-resultCellList.length/Object.keys(studentList).length].className = "td__input td__value td__total td__focus";
			} else {
				resultCellList[position-resultCellList.length/Object.keys(studentList).length].className = "td__input td__value td__result td__focus";
			}
			isFocus[position-resultCellList.length/Object.keys(studentList).length] = true;
			return;
		}
		case "ArrowDown": {
			if(!resultCellList[position+resultCellList.length/Object.keys(studentList).length]) return;
			disableCell(position)
			if(Array.from(resultCellList[position+resultCellList.length/Object.keys(studentList).length].classList).includes('td__total')) {
				resultCellList[position+resultCellList.length/Object.keys(studentList).length].className = "td__input td__value td__total td__focus";
			} else {
				resultCellList[position+resultCellList.length/Object.keys(studentList).length].className = "td__input td__value td__result td__focus";
			}
			isFocus[position+resultCellList.length/Object.keys(studentList).length] = true;
			return;
		}
		case "ArrowLeft": {
			if(!resultCellList[position-1]) return;
			disableCell(position)
			if(Array.from(resultCellList[position-1].classList).includes('td__total')) {
				resultCellList[position-1].className = "td__input td__value td__total td__focus";
			} else {
				resultCellList[position-1].className = "td__input td__value td__result td__focus";
			}
			isFocus[position-1] = true;
			return;
		}
		case "ArrowRight": {
			if(!resultCellList[position+1]) return;
			disableCell(position)
			if(Array.from(resultCellList[position+1].classList).includes('td__total')) {
				resultCellList[position+1].className = "td__input td__value td__total td__focus";
			} else {
				resultCellList[position+1].className = "td__input td__value td__result td__focus";
			}
			isFocus[position+1] = true;
			return;
		}
	}
}

function setRemoveLessonEvents() {
	let lessonRemoveList = document.querySelectorAll('.lesson__remove');
	let removeAccept = document.querySelectorAll('.btn__remove');
	let removeDecline = document.querySelectorAll('.btn__cansel');
	let removeConfirmList = document.querySelectorAll('.remove__confirm');

	for(let i = 0; i < lessonRemoveList.length; i++) {
		lessonRemoveList[i].addEventListener('click', () => {
			lessonRemoveList[i].className = 'hidden lesson__remove';
			removeConfirmList[i].className = 'visible remove__confirm';
			makeStaticTable();
		})
	}

	for(let i = 0; i < removeDecline.length; i++) {
		removeDecline[i].addEventListener('click', () => {
			lessonRemoveList[i].className = 'visible lesson__remove';
			removeConfirmList[i].className = 'hidden remove__confirm';
			makeStaticTable();
		})
	}

	for(let i = 0; i < removeAccept.length; i++) {
		removeAccept[i].addEventListener('click', () => {
			removeLesson(removeAccept[i].getAttribute('location'));

			createTable(dataMatrix, serviceInfo, studentList);
			makeStaticTable(dataMatrix);
			addEvents();
			initializationButton(tableBlock);
			initializationServiceEvents();
		})
	}
}

/* Запросы на сервер */

function removeLesson(lessonId) {
	let lesson = dataMatrix[0][lessonId]
	delete serviceInfo[lessonId];
	dataMatrix = dataMatrix.map((data) => {
		return data.slice(0, lessonId).concat(data.slice(+lessonId+1))
	});

	let removeLessonRequest = new XMLHttpRequest();
	removeLessonRequest.open("GET", `/teacher/ajaxRemoveLesson/${lesson}`, true);
	removeLessonRequest.send();
}

function updateResult(td, tr) {
	let resultList = document.querySelectorAll(`td[location="tr__${tr} td__${td}"] span`);
	//console.log(resultList);

	let inputData = [];
	for(let i = 0; i < resultList.length; i++) {
		inputData.push(resultList[i].innerHTML.trim());
	}
	inputData = JSON.stringify(inputData);

	let student = dataMatrix[tr][0];
	let lesson = dataMatrix[0][td];

	//console.log(student, lesson);
	//console.log(inputData);

	if(["attestation", "semester", "exam", "total"].includes(lesson)) {
		//console.log("TotalInfo");
		if( dataMatrix[tr][dataMatrix[0].length-1] == null &&
			dataMatrix[tr][dataMatrix[0].length-2] == null &&
			dataMatrix[tr][dataMatrix[0].length-3] == null &&
			dataMatrix[tr][dataMatrix[0].length-4] == null) {
			// запрос на добавление totalInfo

			let addTotalInfo = new XMLHttpRequest();
			addTotalInfo.open("POST", "/teacher/ajaxAddTotalInfo", true);
			addTotalInfo.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			addTotalInfo.send(`data=${inputData}&student=${student}&type=${lesson}&discipline=${selectDiscipline.value}&year=${selectYear.value}`);

			//console.log("addTotalInfo");
		} else {
			// запрос на обновление totalinfo
			let updateTotalInfo = new XMLHttpRequest();
			updateTotalInfo.open("POST", "/teacher/ajaxUpdateTotalInfo", true);
			updateTotalInfo.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			updateTotalInfo.send(`data=${inputData}&student=${student}&type=${lesson}&discipline=${selectDiscipline.value}&year=${selectYear.value}`);

			//console.log("updateTotalInfo");
		}

		dataMatrix[tr][td] = JSON.parse(inputData)[0];
		//console.log(dataMatrix);
	} else {
		//console.log("result");

		if(dataMatrix[tr][td]) {
			//отправить запрос на обновление result
			let updateResult = new XMLHttpRequest();
			updateResult.open("POST", "/teacher/ajaxUpdateResult", true);
			updateResult.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			updateResult.send(`data=${inputData}&lesson=${lesson}&student=${student}`);

			//console.log("updateResult")
			dataMatrix[tr][td][1] = JSON.parse(inputData);
			//console.log(dataMatrix);
		} else {
			//отправить запрос на добавление result

			let addResult = new XMLHttpRequest();
			addResult.open("POST", "/teacher/ajaxAddResult", true);
			addResult.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			addResult.send(`data=${inputData}&student=${student}&lesson=${lesson}`);

			//console.log("addResult")
			dataMatrix[tr][td] = [0, JSON.parse(inputData)];
			//console.log(dataMatrix);
		}

		changeAverageMark(tr-1);
	}
}

function updateServiceInfo(serviceId, type, lessonNumber, date, theme) {
	let inputData = [lessonNumber, type, date, theme];
	inputData = JSON.stringify(inputData);

	if(serviceId < 0) {
		// добавление

		let addServiceInfo = new XMLHttpRequest();
		addServiceInfo.open("POST", "/teacher/ajaxAddServiceInfo", true);
		addServiceInfo.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		addServiceInfo.send(`data=${inputData}&group=${selectGroup.value}&discipline=${selectDiscipline.value}&year=${selectYear.value}`);

		// кинуть запрос и с сервера получить id урока

		addServiceInfo.onreadystatechange = function() {
			if(this.readyState == 4 && this.status == 200) {
				let id = this.response;

				// добавить id в serviceInfo и dataMatrix
				delete serviceInfo[serviceId];
				serviceInfo[id] = {type: type, lessonNumber: lessonNumber, date: date, theme: theme};
				dataMatrix[0][dataMatrix[0].indexOf(serviceId)] = id;

				//console.log(dataMatrix);
				//console.log(serviceInfo);

			}
		}


		//console.log("addServiceInfo")
	} else {
		// обновление

		let updateServiceInfo = new XMLHttpRequest();
		updateServiceInfo.open("POST", "/teacher/ajaxUpdateServiceInfo", true);
		updateServiceInfo.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		updateServiceInfo.send(`data=${inputData}&id=${serviceId}`);

		serviceInfo[serviceId] = {type: type, lessonNumber: lessonNumber, date: date, theme: theme};
		//console.log(serviceInfo);
		//console.log("updateServiceInfo")
	}
}

function updateSubgroupInfo(studentId, subgroup, disciplineId, yearId, groupId) {
	let updateSubgroupInfoRequest = new XMLHttpRequest();
	updateSubgroupInfoRequest.open("POST", `/teacher/ajaxUpdateSubgroupInfo`, true);
	updateSubgroupInfoRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	updateSubgroupInfoRequest.send(`groupId=${groupId}&disciplineId=${disciplineId}&yearId=${yearId}&subgroupNumber=${subgroup}&studentId=${studentId}`);
	//console.log(studentId, subgroup, disciplineId, yearId, groupId);
	subroupInfo[studentId] = subgroup;
}

/* Подсчёт среднего балла */

function calculateAverageResult(tr) {
	let countMarks = 0;
	let averageMark = 0;
	for(let td = 1; td < dataMatrix[0].length-4; td++) {
		if(dataMatrix[tr][td]) {
			for(let markIndex = 0; markIndex < dataMatrix[tr][td][1].length; markIndex++) {
				if(/[0-9]/.test(dataMatrix[tr][td][1][markIndex])
					&& !/(зач|н\/п|у\/п|п)/.test(dataMatrix[tr][td][1][markIndex])) {
					mark = dataMatrix[tr][td][1][markIndex].split("/");
					for(let markPart = 0; markPart < mark.length; markPart++) {
						mark[markPart] = parseInt(mark[markPart].replace(/[^\d]/g, ''));
						if([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].includes(parseInt(mark[markPart]))) {
							averageMark += +mark[markPart];
							countMarks++;
						}
					}
				}
			}
		}
	}
	if(averageMark) {
		return (averageMark/countMarks).toFixed(2);
	}
	return 0;
}

/* Динамическое изменене среднего балла*/

function changeAverageMark(tr) {
	let averageMarkList = document.querySelectorAll(".td__averageMark");

	averageMarkList[tr].innerHTML = calculateAverageResult(tr+1);
}

/* изменение цвета отметки */

function getResultColor(result) {
	console.log(result)
	if(["зач", "н/п", "у/п", "п"].includes(result.replace(/(<font style="vertical-align: inherit;">|<\/font>)/g, ''))) {
		return `<span class="${resultColor[result.replace(/(<font style="vertical-align: inherit;">|<\/font>)/g, '')]}">${result}</span>`
	}
	result = result?.split('/') || [""];
	if(result.length < 2) {
		return `<span class="${resultColor[result[0].replace(/(<font style="vertical-align: inherit;">|<\/font>)/g, '')]}">${result[0]}</span>`
	}
	if(resultColor[result[1].replaceAll(/(<font style="vertical-align: inherit;">|<\/font>)/g, '')]) {
		return `<span class="td__red">${result.join('/')}</span>`
	}
	return `<span>${result.join('/')}</span>`
}
