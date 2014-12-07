var tmp='/*\n';$('table:eq(1)').find('tr:gt(1)').each(function() {
	var regex = /^(backgroundColor|chartArea|colors|events|fontSize|fontName|height|legend|title|titlePosition|titleTextStyle|tooltip|width)/;

	var optRegex = /^(hAxis|vAxis)/;


	if ( ! regex.test($('td:eq(0)', this).text()) && ! optRegex.test($('td:eq(0)', this).text()))
	{
		tmp+=$('td:eq(0)', this).text()+' - ';
		tmp+=$('td:eq(1)', this).text()+' - ';
		tmp+=$('td:eq(2)', this).text()+' - ';
		tmp+='[['+$('td:eq(3)', this).text()+']]\n';
	}
});
tmp+='\n*/'
console.log(tmp);
