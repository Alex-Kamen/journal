let getMainDataRequest = new XMLHttpRequest();

getMainDataRequest.onreadystatechange = function() {
	if(this.readyState == 4 && this.status == 200) {
		let [disciplineList, 
			chairList, 
			groupList, 
			yearList] = JSON.parse(this.response);

		console.log(disciplineList);
		console.log(chairList);
		console.log(groupList);
		console.log(yearList);

		let delChairSelect = document.querySelector('select[name="del__discipline__chair"]');

		delChairSelect.addEventListener('input', () => {
			let select = document.querySelector('.del__discipline__discipline')
			select.innerHTML = setDisciplineSelect(disciplineList, "del__discipline__discipline", delChairSelect.value);
		})

		let editChairSelect = document.querySelector('select[name="edit__discipline__chair1"]');

		editChairSelect.addEventListener('input', () => {
			let select = document.querySelector('.edit__discipline__discipline')
			select.innerHTML = setDisciplineSelect(disciplineList, "edit__discipline__discipline", editChairSelect.value);
			document.querySelector('.block__edit__discipline__discipline').innerHTML = "";


			let submit = document.querySelector('input[name="edit__discipline"]');
			let disciplineSelect = document.querySelector('select[name="edit__discipline__discipline"]');

			disciplineSelect.addEventListener('input', () => {
				let block = setInputField('edit__discipline__name', 
										disciplineList[disciplineSelect.value]['name'], 
										'Название');
				block += setInputField('edit__discipline__shortName', 
										disciplineList[disciplineSelect.value]['shortName'], 
										'Сокращённое название');
				block += setChairSelect(chairList, disciplineList[disciplineSelect.value]['chairId']);

				document.querySelector('.block__edit__discipline__discipline').innerHTML = block;
				
			})
		})

		let editChairSelect2 = document.querySelector('select[name="edit__chair__chair"]');

		editChairSelect2.addEventListener('input', () => {
			let block = setInputField('edit__chair__name', 
										chairList[editChairSelect2.value]['name'], 
										'Название');
			block += setInputField('edit__chair__shortName', 
									chairList[editChairSelect2.value]['shortName'], 
									'Сокращённое название');

			document.querySelector('.block__edit__chair__chair').innerHTML = block;
		})

		let editGroupSelect = document.querySelector('select[name="edit__group__group"]');

		editGroupSelect.addEventListener('input', () => {
			let value = "";
			groupList.forEach((elem) => {
				if(elem['id'] == editGroupSelect.value) {
					value = elem['name'];
				}
			})
			document.querySelector('.block__edit__group__group').innerHTML = setInputField('edit__group__name', 
																									value, 
																									'Название');
		})

		let editYearSelect = document.querySelector('select[name="edit__year__name"]');

		editYearSelect.addEventListener('input', () => {
			let block = setInputField('edit__year__year', 
										yearList[editYearSelect.value].split("_")[0], 
										'Год');
			if(yearList[editYearSelect.value].split("_")[1] == 1) {
				block += `<div class="select">
						<svg class="select__btn">
							<use xlink:href="#arrow"></use>
						</svg>
						<select name="edit__year__semester">
							<option  disabled>Семестр</option>
							<option selected value="1">1</option>
							<option value="2">2</option>
						</select>
					</div>`;
			} else {
				block += `<div class="select">
						<svg class="select__btn">
							<use xlink:href="#arrow"></use>
						</svg>
						<select name="edit__year__semester">
							<option disabled>Семестр</option>
							<option value="1">1</option>
							<option selected value="2">2</option>
						</select>
					</div>`;
			}
			
			document.querySelector('.block__edit__year__name').innerHTML = block;
		})
	}
}

getMainDataRequest.open("GET", `/admin/ajax/other`, true);
getMainDataRequest.send();


function setDisciplineSelect(disciplineList, selectName, chairId) {
	let select = `  <svg class="select__btn">
						<use xlink:href="#arrow"></use>
					</svg>
					<select name="${selectName}">
						<option selected disabled>Дисциплина</option>`;

	for(discipline in disciplineList) {
		if(chairId == disciplineList[discipline]['chairId']) {
			select += `<option value="${discipline}">
							${disciplineList[discipline]['shortName']}
						</option>`;
		}
	}
	select += `</select>`;

	return select;

}

function setInputField(inputName, value, placeholder) {
	return `<input type="text" name="${inputName}" placeholder="${placeholder}" class="input__field" value="${value}">`
}

function setChairSelect(chairList, chairId) {
	let select = `  <svg class="select__btn">
						<use xlink:href="#arrow"></use>
					</svg>
					<select name="edit__discipline__chair2">
						<option disabled>Дисциплина</option>`;

	for(chair in chairList) {
		if(chairId == chair) {
			select += `<option selected value="${chair}">
							${chairList[chair]['shortName']}
						</option>`;
		} else {
			select += `<option value="${chair}">
							${chairList[chair]['shortName']}
						</option>`;
		}
	}
	select += `</select>`;

	return select;
}