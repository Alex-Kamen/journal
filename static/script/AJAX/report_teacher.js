let request = new XMLHttpRequest();

let passType = {
	"н/п" : `<option value="н/п" selected>н/п</option>
			<option value="у/п">у/п</option>
			<option value="п">п</option>`,
	"у/п" : `<option value="н/п">н/п</option>
			<option value="у/п" selected>у/п</option>
			<option value="п">п</option>`,
	"п" : `	<option value="н/п">н/п</option>
			<option value="у/п">у/п</option>
			<option value="п" selected>п</option>`
}

const passColor = {
	'н/п': 'td__red',
	'у/п': 'td__green',
	'п': 'td__yellow',
}

request.onreadystatechange = function() {
	let tableBlock = document.querySelector(".subtable");
	if(!document.querySelector('.lds-ring')) {
		tableBlock.innerHTML = `<div class="lds-ring"><div></div><div></div><div></div><div></div></div>`
	}

	if(this.readyState == 4 && this.status == 200) {
		console.log(this.response);
		let [studentList, disciplineList, passesList, serviseList, passesInfo] = JSON.parse(this.response);

		console.log(studentList);
		console.log(disciplineList);
		console.log(passesList);
		console.log(serviseList);
		console.log(passesInfo);

		let studentIdList = Object.keys(studentList).sort((idA, idB) => {
			if(studentList[idA]['surname'] > studentList[idB]['surname']) {
				return 1;
			}
			if(studentList[idA]['surname'] < studentList[idB]['surname']) {
				return -1;
			}
			return 0;
		});

		let staticTable = `<div class="table">`,
			dynamicTable = `<div class="table"><table><tr>`;

		staticTable += `<table>
						<tr>
							<td>Номер пары</td>
						</tr>`;
		for(let serviseItem in serviseList) {
			for(let lesson in serviseList[serviseItem]) {
				dynamicTable += `<td>${serviseList[serviseItem][lesson][1]}</td>`;
			}
		}
		dynamicTable += `		<td rowspan="3" class="td__red">н/п</td>
						<td rowspan="3" class="td__green">у/п</td>
						<td rowspan="3" class="td__yellow">п</td>
						<td rowspan="3">Итого</td>		
				    </tr><tr>`
		staticTable +=`	<tr>
							<td><div class="td__rotate">Дисциплина</div></td>
						</tr>`;
		for(let serviseItem in serviseList) {
			for(let lesson in serviseList[serviseItem]) {
				dynamicTable += `<td><div class="td__rotate">${disciplineList[serviseList[serviseItem][lesson][0]]}</div></td>`;
			}
		}
		dynamicTable += `</tr><tr>`
		staticTable += `<tr>
							<td>Дата</td>
						</tr>`;
		for(let serviseItem in serviseList) {
			dynamicTable += `<td colspan="${serviseList[serviseItem].length}">${serviseItem}</td>`;
		}
		dynamicTable += `</tr>`;
		for(let student of studentIdList) {
			staticTable += `<tr>
								<td>${studentList[student]['surname']} ${studentList[student]['name'][0]}. ${studentList[student]['patronymic'][0]}.</td>
							</tr>`;
			dynamicTable += `<tr>`;
			for(let serviseItem in serviseList) {
				for(let lesson in serviseList[serviseItem]) {
					if(passesList[student]) {
						if(passesList[student][serviseList[serviseItem][lesson][2]]) {
							if (student == 115) console.log(serviseList[serviseItem][lesson])
							if(["н/п", "у/п", "п"].includes(passesList[student][serviseList[serviseItem][lesson][2]][1][0])) {
								dynamicTable += `<td><div class="select__td">
										<select name="pass" class="${passColor[passesList[student][serviseList[serviseItem][lesson][2]][1][0]]}" location="${student}_${serviseList[serviseItem][lesson][2]}_${passesList[student][serviseList[serviseItem][lesson][2]][0]}">
											${passType[passesList[student][serviseList[serviseItem][lesson][2]][1][0]]}
										</select>
										<div>
											<svg class="select__btn">
												<use xlink:href="#arrow"></use>
											</svg>
										</div>
									</div></td>`;
							} else if (["н/п", "у/п", "п"].includes(passesList[student][serviseList[serviseItem][lesson][2]][1][1])) {
								dynamicTable += `<td><div class="select__td">
										<select name="pass" class="${passColor[passesList[student][serviseList[serviseItem][lesson][2]][1][1]]}" location="${student}_${serviseList[serviseItem][lesson][2]}_${passesList[student][serviseList[serviseItem][lesson][2]][0]}">
											${passType[passesList[student][serviseList[serviseItem][lesson][2]][1][1]]}
										</select>
										<div>
											<svg class="select__btn">
												<use xlink:href="#arrow"></use>
											</svg>
										</div>
									</div></td>`;
							} else {
								dynamicTable += `<td></td>`;
							}

/*							if (["н/п", "у/п", "п"].includes(passesList[student][serviseList[serviseItem][lesson][2]][1][1])) {

							} else {
								dynamicTable += `<td></td>`;
							}*/

						} else {
							dynamicTable += `<td></td>`;
						}
					} else {
						dynamicTable += `<td></td>`;
					}
				}
			}
			if(passesInfo[student]) {
				dynamicTable += `  <td class="td__red">${passesInfo[student][0]}</td>
							<td class="td__green">${passesInfo[student][1]}</td>
							<td class="td__yellow">${passesInfo[student][2]}</td>
							<td>${passesInfo[student][0] + passesInfo[student][1] + passesInfo[student][2]}</td>
						</tr>`;
			} else {
				dynamicTable += `<td class="td__red">0</td>
						  <td class="td__green">0</td>
						  <td class="td__yellow">0</td>
						  <td>0</td>
						</tr>`;
			}
			dynamicTable += `</tr>`
		}

		staticTable += `</table></div>`;
		dynamicTable += `</table></div>`;


		let tableBlock = document.querySelector(".subtable");

		let button = `<div class="toExcel__button">Сохранить в Excel</div>`;

		tableBlock.innerHTML = staticTable + dynamicTable;
		makeStaticTable();

		document.querySelector('.toExcel').innerHTML = button;

		document.querySelector('.toExcel__button').addEventListener('click', toExcel);

		let passSelect = document.querySelectorAll("select[name='pass']");

		for(let i = 0; i < passSelect.length; i++) {
			passSelect[i].addEventListener('input', () => {
				passSelect[i].className = passColor[passSelect[i].value]
				let [studentId, lessonId, resultId] = passSelect[i].getAttribute('location').split('_');
				let results = passesList[studentId][lessonId][1];
				results[0] = passSelect[i].value;
				results = JSON.stringify(results);
				console.log(results);
				let xhr = new XMLHttpRequest();
				xhr.open("POST", "/teacher/ajaxUpdateResult", true);
				xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xhr.send(`data=${results}&lesson=${lessonId}&student=${studentId}`);
			});
		}
	}
}


let monthSelect = document.querySelector("select[name='month']");
let yearSelect = document.querySelector("select[name='year']");

monthSelect.addEventListener("input", () => {
	if(parseInt(monthSelect.value) && parseInt(yearSelect.value)) {
		request.open("GET", `/AJAX/teacher/report/${monthSelect.value}/${yearSelect.value}`, true);
		request.send();
	}
});

yearSelect.addEventListener("input", () => {
	if(parseInt(monthSelect.value) && parseInt(yearSelect.value)) {
		request.open("GET", `/AJAX/teacher/report/${monthSelect.value}/${yearSelect.value}`, true);
		request.send();
	}
});

function toExcel() {
	let setExcelFileRequest = new XMLHttpRequest();
	setExcelFileRequest.open("GET", `/AJAX/setReportToExcel/0/${monthSelect.value}/${yearSelect.value}`)
	setExcelFileRequest.send()

	setExcelFileRequest.onreadystatechange = function () {
		if(this.readyState == 4 && this.status == 200) {
			window.location = `/static/xls/рапортичка_0_${monthSelect.value}_${yearSelect.value}.xlsx?${Date.now()}`
		}
	}
}
