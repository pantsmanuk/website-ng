function checkMatch(posPhrase, negPhrase){
	
	if(posPhrase == ''){
		posPhrase = 'Fields match'
	}
	
	if(negPhrase == ''){
		negPhrase = 'Fields do not match'
	}

	var suppliedPass1 = document.getElementById('checkfield1').value;
	var suppliedPass2 = document.getElementById('checkfield2').value;
	
	if(suppliedPass1 == suppliedPass2){
		document.getElementById('credmatch').innerHTML = posPhrase;
		document.getElementById('credmatch').style.backgroundColor = "green"; 
	}
	else{
		document.getElementById('credmatch').innerHTML = negPhrase;
		document.getElementById('credmatch').style.backgroundColor = "red"; 
	}
	
}