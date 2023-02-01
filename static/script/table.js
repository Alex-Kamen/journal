function makeStaticTable() {
	let trCountStatic = document.querySelectorAll(".table:nth-child(1) tr");
	let trCountDynamic = document.querySelectorAll(".table:nth-child(2) tr");

	let tdCountStatic = document.querySelectorAll(".table:nth-child(1) tr td:nth-child(1)");
	//let tdCountDynamic = document.querySelectorAll(".table:nth-child(2) tr td:nth-child(2)");

	let dynamicIndex = 0

	for(let i = 0; i < tdCountStatic.length; i++) {
		let rowSpan = tdCountStatic[i].getAttribute('rspan');
		//console.log(rowSpan)
		if(rowSpan) {
			let height = 0;
			for(let j = 0; j < rowSpan; j++) {
				height += trCountDynamic[dynamicIndex+j].offsetHeight;
			}
			trCountStatic[i].style.height = height + 'px';
			dynamicIndex += +rowSpan
		} else {
			trCountStatic[i].style.height = trCountDynamic[dynamicIndex].offsetHeight + 'px';
			dynamicIndex++;
		}
	}

	dynamicIndex = 0

	for(let i = 0; i < tdCountStatic.length; i++) {
		let rowSpan = tdCountStatic[i].getAttribute('rspan');
		if(rowSpan) {
			let height = trCountStatic[i].offsetHeight;
			for(let j = 0; j < rowSpan; j++) {
				trCountDynamic[j+dynamicIndex].style.height = height/rowSpan + 'px';
			}
			dynamicIndex += +rowSpan
		} else {
			trCountDynamic[dynamicIndex].style.height = trCountStatic[i].offsetHeight + 'px';
			dynamicIndex++;
		}
	}
}
	