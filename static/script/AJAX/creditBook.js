let request = new XMLHttpRequest();

request.onreadystatechange = function() {
	let tableBlock = document.querySelector(".creditBook__table");
	if(!document.querySelector('.lds-ring')) {
		tableBlock.innerHTML = `<div class="lds-ring"><div></div><div></div><div></div><div></div></div>`
	}

	if(this.readyState == 4 && this.status == 200) {
		let [data, yearList, disciplineList] = JSON.parse(this.response)

		console.log(data);
		console.log(yearList);
		console.log(disciplineList);

		let tableList = [];

		for(let year in data) {
			let yearValue = yearList[year].split("_")[0];
			let semester = yearList[year].split("_")[1];
			let table = `<div><table>
							<tr>
								<td colspan="2">${yearValue} - ${semester} семестр</td>
							</tr>
							<tr>
								<td>Дисциплина</td>
								<td>Оценка</td>
							</tr>`;
			for(let discipline in data[year]) {
				table += `<tr>
							<td>${disciplineList[discipline]}</td>
							<td>${data[year][discipline] ? data[year][discipline] : ""}</td>
						</tr>`;
			}
			table += `</table></div>`;
			tableList.push(table);
		}

		let tableBlock = document.querySelector('.creditBook__table');
		let table = "";
		for(let i = 0; i < tableList.length; i++) {
			table += tableList[i];
		}
		tableBlock.innerHTML = table;

	}
}

try {
	let studentSelect = document.querySelector("select[name='student']");

	studentSelect.addEventListener('input', () => {
		request.open("GET", `/creditBook/${+studentSelect.value}`);
		request.send();
	});
} catch {

}

try {
	let groupSelect = document.querySelector("select[name='group']");
	groupSelect.addEventListener('input', () => {
		let selectBlock  = document.querySelector(".select:nth-child(2)");
		let xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if(this.readyState == 4 && this.status == 200) {
				let studentList = JSON.parse(this.response);
				let studentSelect = `<svg class="select__btn">
											<use xlink:href="#arrow"></use>
										</svg>
										<select name="student">
											<option selected disabled>Студент</option>`;
				for(let student in studentList) {
					studentSelect += `<option value="${student}">
												${studentList[student]['surname']} ${studentList[student]['name']}
											</option>`;
				}
				studentSelect += `</select>`
				selectBlock.innerHTML = studentSelect;
				studentSelect = document.querySelector("select[name='student']");
				studentSelect.addEventListener('input', () => {
					if(parseInt(studentSelect.value)) {
						request.open("GET", `/creditBook/${+studentSelect.value}`);
						request.send();
					}
				});
			}
		}
		if(parseInt(groupSelect.value)) {
			xhr.open('GET', `/AJAX/studentList/${+groupSelect.value}`);
			xhr.send();
		}
	});
} catch {

}





