function checkAll(){
	document.querySelectorAll("table.seekedTable tbody tr td input[type='checkbox']")
		.forEach(x => x.checked = document.querySelector("table.seekedTable thead tr th input[type='checkbox']").checked)
}