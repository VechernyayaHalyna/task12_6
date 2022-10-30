<?php
// Функция получения ФИО из частей (отдельных фамилии, имени и отчества)
function getFullnameFromParts($surname, $name,  $middlename){
    return $surname."\x20".$name."\x20".$middlename;
}

//Функция возвращение массива из трёх элементов с ключами ‘name’, ‘surname’ и ‘patronomyc’ из ФИО
function getPartsFromFullname($fullName){
    $chunkName = []; 
    $startNum = 0;
    $strLen=mb_strlen($fullName);
    
    for($i=0; $i<$strLen; $i++)
    {
        if(mb_ord(mb_substr($fullName,$i,1))==32)
        {
            array_key_exists('surname',$chunkName) ? $chunkName['name'] = mb_substr($fullName, $startNum, $i-$startNum):$chunkName['surname'] = mb_substr($fullName, $startNum, $i-$startNum);
            $startNum = $i+1;
        }
        
        if($i==$strLen-1 && $startNum>0){
            array_key_exists('name',$chunkName) ? $chunkName['patronomyc'] = mb_substr($fullName, $startNum):$chunkName['Name'] = mb_substr($fullName, $startNum);
        }
    }
    return $chunkName;
}

// Функция сокращения фамилии и отбрасывания отчества
function getShortName($fullName){
    $nameParts = getPartsFromFullname($fullName);
    return $nameParts['surname']."\x20".mb_substr($nameParts['name'], 0, 1).".";
}

// Функция возвращения пола
function getGenderFromName($fullName){
    $nameParts = getPartsFromFullname($fullName);
    $genderMale=0;
    $genderFeMale=0;   
    
    if(mb_substr($nameParts['surname'], -1) =='в') $genderMale+=1;
    elseif(mb_substr($nameParts['surname'], -2)=='ва') $genderFeMale+=1;
    
    if(mb_substr($nameParts['name'], -1) =='й' || mb_substr($nameParts['name'], -1) =='н')  $genderMale+=1;
    elseif(mb_substr($nameParts['name'], -1) =='а') $genderFeMale+=1;
    
    if(mb_substr($nameParts['patronomyc'], -2) =='ич')  $genderMale+=1;
    elseif(mb_substr($nameParts['patronomyc'], -3) =='вна') $genderFeMale+=1;    
    
    return $genderMale<=>$genderFeMale;
}

//Функция определения полового состава аудитории
function getGenderDescription($auditory){
    $results;
    $maleResult=0;
    $femaleResult=0;   
    $undefinedResult=0;      
    
    foreach($auditory as $person)
    {
        $results[]=getGenderFromName($person['fullname']);
    }
    echo "Мужчины - ".round(count(array_filter($results, function($num)
    {
        if ($num == 1) return true;
        else return false;
    }))/count($results),2).'%'.'<br>';;
    echo "Женщины - ".round(count(array_filter($results, function($num)
    {
        if ($num == -1) return true;
        else return false;
    }))/count($results),2).'%'.'<br>';;
    echo "Не удалось определить - ".round(count(array_filter($results, function($num)
    {
        if ($num == 0) return true;
        else return false;
    }))/count($results),2).'%'.'<br>';;   
}

//Функция определения идеальной пары
function  getPerfectPartner($surname, $name, $middlename, $auditory){
    $normalisedName =  getFullnameFromParts(mb_convert_case($surname, MB_CASE_TITLE),mb_convert_case($name, MB_CASE_TITLE),mb_convert_case($middlename, MB_CASE_TITLE)); 
    $curGender=getGenderFromName($normalisedName);
    $pairPerson=null;
    do{
        $pairPerson = $auditory[rand(0,count($auditory)-1)];
        if(getGenderFromName($pairPerson['fullname'])==$curGender) $pairPerson=null;
    } while($pairPerson==null);
    echo getShortName($normalisedName).'+'.getShortName($pairPerson['fullname']).'='.'<br>';
    echo "♡ Идеально на ".rand(50,100)."% ♡".'<br>';;
}