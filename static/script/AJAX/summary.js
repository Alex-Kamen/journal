let request = new XMLHttpRequest();

const resultColor = {
	0: 'td__red',
	1: 'td__red',
	2: 'td__red',
	'attestation': 'td__blue',
	'semester': 'td__green',
	'exam': 'td__yellow',
}

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
		let [studentList, disciplineList, summaryInfo, passesInfo] = JSON.parse(this.response);

		let studentIdList = Object.keys(studentList).sort((idA, idB) => {
			if(studentList[idA]['surname'] > studentList[idB]['surname']) {
				return 1;
			}
			if(studentList[idA]['surname'] < studentList[idB]['surname']) {
				return -1;
			}
			return 0;
		});

		console.log(studentList);
		console.log(disciplineList);
		console.log(summaryInfo);
		console.log(passesInfo);

		let staticTable = `<div class="table"><table>`,
			dynamicTable = `<div class="table"><table>`;

		staticTable += `<tr>
							<td>
								<div class="td__rotate"><p>Дисциплина</p></div>
							</td>
						</tr>`;
		dynamicTable += `<tr>`;
		for(let disciplineId in summaryInfo) {
			dynamicTable += `<td>
						<div class="td__rotate"><p>${disciplineList[disciplineId]}</p></div>
					</td>`;
		}
		dynamicTable += `<td rowspan="" class="td__red">н/п</td>
				<td rowspan="" class="td__green">у/п</td>
				<td rowspan="" class="td__yellow">п</td>
				<td rowspan="">Итого</td>
			</tr>`;
		for(let studentId of studentIdList) {
			staticTable += `<tr>
								<td>
									${studentList[studentId]['surname']} ${studentList[studentId]['name'][0]}. ${studentList[studentId]['patronymic'][0]}.
								</td>
							</tr>`;
			dynamicTable += `<tr>`;
			for(disciplineId in summaryInfo) {
				dynamicTable += `<td class="${resultColor[summaryInfo[disciplineId][studentId] ? summaryInfo[disciplineId][studentId][1] : ""]}">
							${getResultColor(summaryInfo[disciplineId][studentId] ? summaryInfo[disciplineId][studentId][0] : "")}
						</td>`;
			}
			if(passesInfo[studentId]) {
				dynamicTable += `<td class="td__red">${passesInfo[studentId][0]}</td>
						<td class="td__green">${passesInfo[studentId][1]}</td>
						<td class="td__yellow">${passesInfo[studentId][2]}</td>
						<td>${passesInfo[studentId][0] + passesInfo[studentId][1] + passesInfo[studentId][2]}</td>
					</tr>`;
			} else {
				dynamicTable += `<td class="td__red">0</td>
						<td class="td__green">0</td>
						<td class="td__yellow">0</td>
						<td>0</td>
					</tr>`;
			}
		}
		dynamicTable += `</table></div>`;
		staticTable += `</table></div>`;

		let button = `<div class="toExcel__button">Сохранить в Excel</div>`

		let tableBlock = document.querySelector(".subtable");
		tableBlock.innerHTML = staticTable + dynamicTable;
		makeStaticTable();

		document.querySelector('.toExcel').innerHTML = button

		document.querySelector('.toExcel__button').addEventListener('click', toExcel);
	}
}

let groupSelect = document.querySelector("select[name='group']");
let yearSelect = document.querySelector("select[name='year']");

if(groupSelect) {
	groupSelect.addEventListener('input', () => {
		if(parseInt(groupSelect.value) && parseInt(yearSelect.value)) {
			request.open("GET", `/AJAX/administrator/summaryData/${groupSelect.value}/${yearSelect.value}`, true);
			request.send();
		}
	});

	yearSelect.addEventListener('input', () => {
		if(parseInt(groupSelect.value) && parseInt(yearSelect.value)) {
			request.open("GET", `/AJAX/administrator/summaryData/${groupSelect.value}/${yearSelect.value}`, true);
			request.send();
		}
	});

} else {
	yearSelect.addEventListener('input', () => {
		if(parseInt(yearSelect.value)) {
			request.open("GET", `/AJAX/teacher/summaryData/${yearSelect.value}`, true);
			request.send();
		}
	});
}

function toExcel() {
	let setExcelFileRequest = new XMLHttpRequest();
	setExcelFileRequest.open("GET", `/AJAX/setSummaryToExcel/${groupSelect ? groupSelect.value : 0}/${yearSelect.value}`)
	setExcelFileRequest.send()

	setExcelFileRequest.onreadystatechange = function () {
		if(this.readyState == 4 && this.status == 200) {
			window.location = `/static/xls/сводный_лист_${groupSelect ? groupSelect.value : 0}_${yearSelect.value}.xlsx?${Date.now()}`
		}
	}
}

