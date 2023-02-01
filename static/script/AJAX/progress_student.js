let studentResultsRequest = new XMLHttpRequest();

const lessonTypes = {
	'Тип':  `Тип`,
	'defoult':  `Обыч`,
	'otr': `С отр`,
}

const resultColor = {
	0: 'td__red',
	1: 'td__red',
	2: 'td__red',
	'н/п': 'td__red',
	'п': 'td__yellow',
	'у/п': 'td__green'
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


let yearSelect = document.querySelector("select[name='year']");

yearSelect.addEventListener('input', () => {
	if(parseInt(yearSelect.value)) {
		studentResultsRequest.open("GET", `/student/AJAXProgress/${yearSelect.value}`, true);
		studentResultsRequest.send()
	}
});

studentResultsRequest.onreadystatechange = function() {
	let tableBlock = document.querySelector(".progress__content");
	if(!document.querySelector('.lds-ring')) {
		tableBlock.innerHTML = `<div class="lds-ring"><div></div><div></div><div></div><div></div></div>`
	}

	if(this.readyState == 4 && this.status == 200) {
		let [disciplineList, passesInfo, averageResult, totalInfoList, serviceInfo, resultsList] = JSON.parse(this.response);

		console.log(disciplineList);
		console.log(passesInfo);
		console.log(averageResult);
		console.log(totalInfoList);
		console.log(serviceInfo);
		console.log(resultsList);
		console.log("___");

		let block = ``;

		for(discipline of Object.keys(serviceInfo)) {
			let staticTable = ``;
			let dynamicTable = ``;

			let serviceInfoList = Object.keys(serviceInfo[discipline]).sort((idA, idB) => {
				let dateA = serviceInfo[discipline][idA]['date'].split("-");
				let dateB = serviceInfo[discipline][idB]['date'].split("-");
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

			block += `<div class="progress__item">
					 	<h3 class="progress__title">
					 		${disciplineList[discipline]['name']}
					 	</h3>
					 	<div class="subtable">`;

			staticTable =`<div class="table">
							<table>
						 		<tr>
						 			<td rspan="3">Дисциплина</td>
						 			<td rspan="3" class="td__red">н/п</td>
						 			<td rspan="3" class="td__green">у/п</td>
						 			<td rspan="3" class="td__yellow">п</td>
						 			<td rspan="3">ср/б</td>
								</tr>`;
			dynamicTable = `<div class="table">
							<table>
						 		<tr>`;
			for(lesson of serviceInfoList) {
				let lessonType = 1;
				if(serviceInfo[discipline][lesson]['type'] == 'otr') lessonType = 2;

				dynamicTable += `<td colspan="${lessonType}">
									${serviceInfo[discipline][lesson]['date']}
								</td>`;
			}

			dynamicTable += `   <td rowspan="3" class="td__blue">Аттестация</td>
								<td rowspan="3" class="td__green">Семестр</td>
								<td rowspan="3" class="td__yellow">Экзамен</td>
								<td rowspan="3">В зачётку</td>
							</tr>
							<tr>`;

			for(lesson of serviceInfoList) {
				let lessonType = 1;
				if(serviceInfo[discipline][lesson]['type'] == 'otr') lessonType = 2;

				dynamicTable += `<td colspan="${lessonType}">
									${lessonTypes[serviceInfo[discipline][lesson]['type']]}
								</td>`;
			}

			dynamicTable += `   </tr>
								<tr>`;

			for(lesson of serviceInfoList) {
				let lessonType = 1;
				if(serviceInfo[discipline][lesson]['type'] == 'otr') lessonType = 2;

				dynamicTable += `<td class="theme" colspan="${lessonType}">
									<svg class="theme__icon">
										<use xlink:href="#theme" class="theme__icon"></use>
									</svg>
									<div class="theme__block hidden">
										<textarea disabled>
											${serviceInfo[discipline][lesson]['theme']}
										</textarea>
									</div>
								</td>`;
			}

			dynamicTable += `</tr>`
			staticTable +=	`<tr>
								<td>${disciplineList[discipline]['shortName']}</td>
								<td class="td__red">${passesInfo[discipline] ? passesInfo[discipline][0] ? passesInfo[discipline][0] : 0 : 0}</td>
								<td class="td__green">${passesInfo[discipline] ? passesInfo[discipline][1] ? passesInfo[discipline][1] : 0 : 0}</td>
								<td class="td__yellow">${passesInfo[discipline] ? passesInfo[discipline][2] ? passesInfo[discipline][2] : 0 : 0}</td>
								<td>${averageResult[discipline]?.toFixed(2) ? averageResult[discipline].toFixed(2) : 0}</td>
							</tr>
						</table>
			 		</div>`;

			dynamicTable += `<tr>`;
			for(lesson of serviceInfoList) {
				if(resultsList[discipline]) {
					if(resultsList[discipline][lesson]) {
						if(serviceInfo[discipline][lesson]['type'] == 'defoult') {
							dynamicTable += `<td>
									${getResultColor(resultsList[discipline][lesson][0])}
								</td>`
						} else if(serviceInfo[discipline][lesson]['type'] == 'otr') {
							dynamicTable += `<td>
									${getResultColor(resultsList[discipline][lesson][0])}
								</td>
								<td>
									${getResultColor(resultsList[discipline][lesson][1])}
								</td>`
						} else {
							dynamicTable += `<td></td>`;
						}
					} else {
						if(serviceInfo[discipline][lesson]['type'] == 'otr') {
							dynamicTable += `<td></td>
								<td></td>`;
						} else {
							dynamicTable += `<td></td>`;
						}
					}
				} else {
					if(serviceInfo[discipline][lesson]['type'] == 'otr') {
						dynamicTable += `<td></td>
								<td></td>`;
					} else {
						dynamicTable += `<td></td>`;
					}
				}
			}

			dynamicTable += `  	<td class="td__blue">${getResultColor(totalInfoList[discipline] ? totalInfoList[discipline]['attestation'] ? totalInfoList[discipline]['attestation'] : "" : "")}</td>
								<td class="td__green">${getResultColor(totalInfoList[discipline] ? totalInfoList[discipline]['semester'] ? totalInfoList[discipline]['semester'] : "" : "")}</td>
								<td class="td__yellow">${getResultColor(totalInfoList[discipline] ? totalInfoList[discipline]['exam'] ? totalInfoList[discipline]['exam'] : "" : "")}</td>
								<td>${getResultColor(totalInfoList[discipline] ? totalInfoList[discipline]['total'] ? totalInfoList[discipline]['total'] : "" : "")}</td>
							</tr>
			 			</table>
			 		</div>`

			block += staticTable + dynamicTable + `</div>`;
		}

		let tableBlock = document.querySelector('.progress__content');

		tableBlock.innerHTML = block;
		makeStaticTable();
		makeStaticTable();
		makeStaticTable();

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