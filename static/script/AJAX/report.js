let request = new XMLHttpRequest();

request.onreadystatechange = function() {
	let tableBlock = document.querySelector(".subtable");
	if(!document.querySelector('.lds-ring')) {
		tableBlock.innerHTML = `<div class="lds-ring"><div></div><div></div><div></div><div></div></div>`
	}

	if(this.readyState == 4 && this.status == 200) {
		console.log(this.response);
		let [studentList, disciplineList, passesList, serviseList, passesInfo] = JSON.parse(this.response);

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
							if(passesList[student][serviseList[serviseItem][lesson][2]][1][0] == "н/п") {
								dynamicTable += `<td class="td__red">${passesList[student][serviseList[serviseItem][lesson][2]][1][0]}</td>`;
							} else if (passesList[student][serviseList[serviseItem][lesson][2]][1][0] == "у/п") {
								dynamicTable += `<td class="td__green">${passesList[student][serviseList[serviseItem][lesson][2]][1][0]}</td>`;
							} else if (passesList[student][serviseList[serviseItem][lesson][2]][1][0] == "п") {
								dynamicTable += `<td class="td__yellow">${passesList[student][serviseList[serviseItem][lesson][2]][1][0]}</td>`;
							} else {
								dynamicTable += `<td></td>`;
							}
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
			dynamicTable += `</tr>`;
		}

		staticTable += `</table></div>`;
		dynamicTable += `</table></div>`;
		

		let tableBlock = document.querySelector(".subtable");

		let button = `<div class="toExcel__button">Сохранить в Excel</div>`

		tableBlock.innerHTML = staticTable + dynamicTable;
		makeStaticTable();

		document.querySelector('.toExcel').innerHTML = button;

		document.querySelector('.toExcel__button').addEventListener('click', toExcel);
	}
}


let groupSelect = document.querySelector("select[name='group']");
let monthSelect = document.querySelector("select[name='month']");
let yearSelect = document.querySelector("select[name='year']");

groupSelect.addEventListener("input", () => {
	if(parseInt(groupSelect.value) && parseInt(monthSelect.value) && parseInt(yearSelect.value)) {
		request.open("GET", `/AJAX/administrator/report/${groupSelect.value}/${monthSelect.value}/${yearSelect.value}`, true);
		request.send();
	}
});

monthSelect.addEventListener("input", () => {
	if(parseInt(groupSelect.value) && parseInt(monthSelect.value) && parseInt(yearSelect.value)) {
		request.open("GET", `/AJAX/administrator/report/${groupSelect.value}/${monthSelect.value}/${yearSelect.value}`, true);
		request.send();
	}
});

yearSelect.addEventListener("input", () => {
	if(parseInt(groupSelect.value) && parseInt(monthSelect.value) && parseInt(yearSelect.value)) {
		request.open("GET", `/AJAX/administrator/report/${groupSelect.value}/${monthSelect.value}/${yearSelect.value}`, true);
		request.send();
	}
});

function toExcel() {
	let setExcelFileRequest = new XMLHttpRequest();
	setExcelFileRequest.open("GET", `/AJAX/setReportToExcel/${groupSelect.value}/${monthSelect.value}/${yearSelect.value}`)
	setExcelFileRequest.send()

	setExcelFileRequest.onreadystatechange = function () {
		if(this.readyState == 4 && this.status == 200) {
			window.location = `/static/xls/рапортичка_${groupSelect.value}_${monthSelect.value}_${yearSelect.value}.xlsx?${Date.now()}`
		}
	}
}