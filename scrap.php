<?php
	@$html = file_get_contents('https://reliefweb.int/country/ind');// for getting the htmlusing @ on starting to avoid warnings 
 $link= new DOMDocument();
libxml_use_internal_errors(TRUE);//disable libxml errors
if(!empty($html)){//check whether the html is returned or not
              $link->loadHTML($html);
	libxml_clear_errors(); //remove errors for yucky html
	
	$link_xpath = new DOMXPath($link);            
    	$percent=$link_xpath->query('//div[@id="content"]/div[@class="region region-content"]/div[@class="river-list river-updates river-sort-latest"]/div[@class="item "]/div[@class="title"]');
    	//print_r($percent);
		            	  
}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		*,
*:before,
*:after {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
}

section {
	position: fixed;
	right: 0px;
  display: block;
  float: right;
  width: 30%;
  top: 20%;
  font-family: monospace;
  color: #555;
}

.title {
  text-align: center;
  padding: 2rem;
}

table {
  width: 100%;
}
table thead {
  background-color: #e4e4e4;
}
table td {
  padding: 15px;
}
table tbody {
  display: block;
  max-height: 160px;
  overflow-y: auto;
}
table tbody td {
  border-bottom: 1px solid #eaeaea;
}
table thead, table tbody tr {
  display: table;
  width: 100%;
  table-layout: fixed;
}

	</style>
</head>
<body>
<section>
<table >
    <thead>
        <tr>
            <td>Recent news about food requirements</td>
           
        </tr>
    </thead>
    <tbody>
    	<?php
       foreach($percent as $r){
               echo "<tr>";
                echo "<td>".$r->nodeValue."</tr>";
            echo "</tr>";
            }    
            ?> 
    </tbody>
</table>
</section>
</body>
</html>