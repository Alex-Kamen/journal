let request = new XMLHttpRequest();

const lessonTypes = {
		'Тип':  `Тип`,
		'defoult':  `Обыч`,
		'otr': `С отр`,
}

const themeTemplate = `
	
`

const resultColor = {
	0: 'td__red',
	1: 'td__red',
	2: 'td__red',
	'н/п': 'td__red',
	'п': 'td__yellow',
	'у/п': 'td__green'
}

let disciplineSelect, yearSelect, groupSelect;

function getResultColor(result) {
	if(["зач", "н/п", "у/п", "п"].includes(result.replace(/(<font style="vertical-align: inherit;">|<\/font>)/g, ''))) {
		return `<span class="${resultColor[result.replace(/(<font style="vertical-align: inherit;">|<\/font>)/g, '')]}">${result}</span>`
	}
	result = result?.split('/') || [""];
	if(result.length < 2) {
		return `<span class="${resultColor[result[0].replace(/(<font style="vertical-align: inherit;">|<\/font>)/g, '')]}">${result[0]}</span>`
	}
	if(resultColor[result[1].replace(/(<font style="vertical-align: inherit;">|<\/font>)/g, '')]) {
		return `<span class="td__red">${result.join('/')}</span>`
	}
	return `<span>${result.join('/')}</span>`
}

request.onreadystatechange = function() {
	let tableBlock = document.querySelector(".subtable");
	if(!document.querySelector('.lds-ring')) {
		tableBlock.innerHTML = `<div class="lds-ring"><div></div><div></div><div></div><div></div></div>`
	}

	if(this.readyState == 4 && this.status == 200) {
		let [studentList, passesInfo, serviceInfo, resultsList, totalInfoList, averageResult] = JSON.parse(this.response);

		console.log(studentList);
		console.log(passesInfo);
		console.log(serviceInfo);
		console.log(resultsList);
		console.log(totalInfoList);
		console.log(averageResult);
		console.log("___");

		let studentIdList = Object.keys(studentList).sort((idA, idB) => {
			if(studentList[idA]['surname'] > studentList[idB]['surname']) {
				return 1;
			}
			if(studentList[idA]['surname'] < studentList[idB]['surname']) {
				return -1;
			}
			return 0;
		});

		let serviceInfoList = Object.keys(serviceInfo).sort((idA, idB) => {
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

		let staticTable = ``, dynamicTable = ``;

		staticTable = `<div class="table"><table>
						<tr>
							<td rspan='3'>ФИО</td>
							<td rspan='3' class="td__red">н/п</td>
							<td rspan='3' class="td__green">у/п</td>
							<td rspan='3' class="td__yellow">п</td>
							<td rspan='3'>cр/б</td>
						</tr>`;
		dynamicTable = `<div class="table"><table>
							<tr>`
		for(let lesson of serviceInfoList) {
			let lessonType = 1;
			if(serviceInfo[lesson]['type'] == 'otr') {
				lessonType = 2;
			}
			dynamicTable += `<td colspan="${lessonType}">${serviceInfo[lesson]['date']}</td>`;
		}
		dynamicTable += `	<td rowspan='3'><div class="td__rotate td__blue">Аттестация</div></td>
							<td rowspan='3'><div class="td__rotate td__green">Семестр</div></td>
							<td rowspan='3'><div class="td__rotate td__yellow">Экзамен</div></td>
							<td rowspan='3'><div class="td__rotate">В зачётку</div></td></tr><tr>`
		for(let lesson in serviceInfo) {
			let lessonType = 1;
			if(serviceInfo[lesson]['type'] == 'otr') {
				lessonType = 2;
			}
			dynamicTable += `<td colspan="${lessonType}">${lessonTypes[serviceInfo[lesson]['type']]}</td>`;
		}
		dynamicTable += `</tr><tr>`;
		for(let lesson of serviceInfoList) {
			let lessonType = 1;
			if(serviceInfo[lesson]['type'] == 'otr') {
				lessonType = 2;
			}
			dynamicTable += `  <td class="theme" colspan="${lessonType}">
							<svg class="theme__icon">
								<use xlink:href="#theme" class="theme__icon"></use>
							</svg>
							<div class="theme__block hidden">
								<textarea disabled>${serviceInfo[lesson]['theme']}</textarea>
							</div>
						</td>`;
		}
		dynamicTable += `</tr>`;

		for(let student of studentIdList) {
			staticTable += `<tr>
						<td>
							${studentList[student]['surname']} ${studentList[student]['name'][0]}. ${studentList[student]['patronymic'][0]}.
						</td>
						<td class="td__red">
							${passesInfo[student] ? passesInfo[student][0] ? passesInfo[student][0] : "0" : "0"}
						</td>
						<td class="td__green">
							${passesInfo[student] ? passesInfo[student][1] ? passesInfo[student][1] : "0" : "0"}
						</td>
						<td class="td__yellow">
							${passesInfo[student] ? passesInfo[student][2] ? passesInfo[student][2] : "0" : "0"}
						</td>
						<td>
							${averageResult[student] ? averageResult[student] : "0"}
						</td>
						</tr>`;
			dynamicTable += `<tr>`
			for(let result of serviceInfoList) {
				if(resultsList[student]) {
					if(resultsList[student][result]) {
						if(serviceInfo[result]['type'] == 'defoult') {
							dynamicTable += `<td>${getResultColor(resultsList[student][result][1][0] ? resultsList[student][result][1][0] : "")}</td>`;
						} else if (serviceInfo[result]['type'] == 'otr') {
							dynamicTable += `	<td>${getResultColor(resultsList[student][result][1][0] ? resultsList[student][result][1][0] : "")}</td>
										<td>${getResultColor(resultsList[student][result][1][1] ? resultsList[student][result][1][1] : "")}</td>`;
						} else if (serviceInfo[result]['type'] == 'Тип') {
							dynamicTable += `<td></td>`;
						}
					} else {
						if (serviceInfo[result]['type'] == 'otr') {
							dynamicTable += `	<td></td>
										<td></td>`;
						} else {
							dynamicTable += `<td></td>`;
						}
					} 
				} else {
					if (serviceInfo[result]['type'] == 'otr') {
						dynamicTable += `	<td></td>
										<td></td>`;
						} else {
						dynamicTable += `<td></td>`;
						}
				}
				
			}
			dynamicTable += `<td class="td__blue">
						${getResultColor(totalInfoList[student] ? totalInfoList[student]['attestation'] ? totalInfoList[student]['attestation'] : "" : "")}
					</td>
					<td class="td__green">
						${getResultColor(totalInfoList[student] ? totalInfoList[student]['semester'] ? totalInfoList[student]['semester'] : "" : "")}
					</td>
					<td class="td__yellow">
						${getResultColor(totalInfoList[student] ? totalInfoList[student]['exam'] ? totalInfoList[student]['exam'] : "" : "")}
					</td>
					<td>
						${getResultColor(totalInfoList[student] ? totalInfoList[student]['total'] ? totalInfoList[student]['total'] : "" : "")}
					</td></tr>`
		}
		staticTable += `</table></div>`;
		dynamicTable += '</table></div>';

		let tableBlock = document.querySelector(".subtable");

		let button = `<div class="toExcel__button">Сохранить в Excel</div>`

		tableBlock.innerHTML = staticTable + dynamicTable;
		makeStaticTable();
		makeStaticTable();
		makeStaticTable();

		document.querySelector('.toExcel').innerHTML = button;

		document.querySelector('.toExcel__button').addEventListener('click', toExcel);

		let themeCellList = document.querySelectorAll('.theme');
		let themeList = document.querySelectorAll('.theme__block');
		let activeTheme = false;

		for(let i = 0; i < themeCellList.length; i++) {
			themeCellList[i].addEventListener('click', (event) => {
				if(event.target.className == "theme" || event.target.className.baseVal == "theme__icon") {
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
			});
		}

	}
}

if(document.querySelector("select[name='group']")) {
	console.log(1)
	let chairSelect = document.querySelector("select[name='chair']");
	groupSelect = document.querySelector("select[name='group']");
	yearSelect = document.querySelector("select[name='year']");

	chairSelect.addEventListener('input', () => {
		if(parseInt(chairSelect.value)) {
			let xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				if(this.readyState == 4 && this.status == 200) {
					let disciplineList = JSON.parse(this.response);
					disciplineSelect = `<svg class="select__btn">
												<use xlink:href="#arrow"></use>
											</svg>
											<select name='discipline'>
												<option selected disabled>Дисциплина</option>`;
					console.log(disciplineList);
					for(let discipline in disciplineList) {
						disciplineSelect += `<option value="${disciplineList[discipline]['id']}">${disciplineList[discipline]['shortName']}</option>`
					}
					disciplineSelect += `</select>`;
					let selectBlock = document.querySelector(".select:nth-child(3)");
					console.log(selectBlock);
					selectBlock.innerHTML = disciplineSelect;
					disciplineSelect = document.querySelector("select[name='discipline']");
					disciplineSelect.addEventListener('input', () => {
						if(parseInt(disciplineSelect.value) && parseInt(groupSelect.value) && parseInt(yearSelect.value)) {
							request.open("GET", `/AJAX/administrator/progress/${disciplineSelect.value}/${groupSelect.value}/${yearSelect.value}`, true);
							request.send()
						}
					});
					groupSelect.addEventListener('input', () => {
						if(parseInt(disciplineSelect.value) && parseInt(groupSelect.value) && parseInt(yearSelect.value)) {
							request.open("GET", `/AJAX/administrator/progress/${disciplineSelect.value}/${groupSelect.value}/${yearSelect.value}`, true);
							request.send()
						}
					});
					yearSelect.addEventListener('input', () => {
						if(parseInt(disciplineSelect.value) && parseInt(groupSelect.value) && parseInt(yearSelect.value)) {
							request.open("GET", `/AJAX/administrator/progress/${disciplineSelect.value}/${groupSelect.value}/${yearSelect.value}`, true);
							request.send()
						}
					});
				}
			}
			xhr.open("GET", `/AJAX/disciplineList/${chairSelect.value}`, true);
			xhr.send();
		}
	});
} else {
	console.log(2)
	let chairSelect = document.querySelector("select[name='chair']");
	yearSelect = document.querySelector("select[name='year']");

	chairSelect.addEventListener('input', () => {
		if(parseInt(chairSelect.value)) {
			let xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				if(this.readyState == 4 && this.status == 200) {
					let disciplineList = JSON.parse(this.response);
					disciplineSelect = `<svg class="select__btn">
												<use xlink:href="#arrow"></use>
											</svg>
											<select name='discipline'>
												<option selected disabled>Дисциплина</option>`;
					console.log(disciplineList);
					for(let discipline in disciplineList) {
						disciplineSelect += `<option value="${disciplineList[discipline]['id']}">${disciplineList[discipline]['shortName']}</option>`
					}
					disciplineSelect += `</select>`;
					let selectBlock = document.querySelector(".select:nth-child(3)");
					console.log(selectBlock);
					selectBlock.innerHTML = disciplineSelect;
					disciplineSelect = document.querySelector("select[name='discipline']");
					disciplineSelect.addEventListener('input', () => {
						if(parseInt(disciplineSelect.value) && parseInt(yearSelect.value)) {
							request.open("GET", `/AJAX/teacher/progress/${disciplineSelect.value}/${yearSelect.value}`, true);
							request.send()
						}
					});
					yearSelect.addEventListener('input', () => {
						if(parseInt(disciplineSelect.value) && parseInt(yearSelect.value)) {
							request.open("GET", `/AJAX/teacher/progress/${disciplineSelect.value}/${yearSelect.value}`, true);
							request.send()
						}
					});
				}
			}
			xhr.open("GET", `/AJAX/disciplineList/${chairSelect.value}`, true);
			xhr.send();
		}
	});
}

function toExcel() {
	let setExcelFileRequest = new XMLHttpRequest();
	setExcelFileRequest.open("GET", `/AJAX/setProgressToExcel/${groupSelect ? groupSelect.value : 0}/${disciplineSelect.value}/${yearSelect.value}`)
	setExcelFileRequest.send()

	setExcelFileRequest.onreadystatechange = function () {
		if(this.readyState == 4 && this.status == 200) {
			window.location = `/static/xls/успеваемость_${groupSelect ? groupSelect.value : 0}_${disciplineSelect.value}_${yearSelect.value}.xlsx?${Date.now()}`
		}
	}
}

