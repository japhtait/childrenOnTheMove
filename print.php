<?php 
  require ("fpdf/fpdf.php");
  require ("word.php");
  require "config.php"; 

  //customer and invoice details
  $info=[
    "customer"=>"",
    "address"=>",",
    "city"=>"",
    "invoice_no"=>"",
    "invoice_date"=>"",
    "total_amt"=>"",
    "words"=>"",
  ];
  
  //Select Invoice Details From Database
  $sql="select * from invoice where SID='{$_GET["id"]}'";
  $res=$con->query($sql);
  if($res->num_rows>0){
	  $row=$res->fetch_assoc();
	  
	  $obj=new IndianCurrency($row["GRAND_TOTAL"]);
    $amount_in_words = $obj->get_words();

	  $info=[
		"customer"=>$row["CNAME"],
		"address"=>$row["CADDRESS"],
		"city"=>$row["CCITY"],
		"invoice_no"=>$row["INVOICE_NO"],
		"invoice_date"=>date("d-m-Y",strtotime($row["INVOICE_DATE"])),
		"total_amt"=>$row["GRAND_TOTAL"],
		"words"=> $amount_in_words,
	  ];
  }

 
  //invoice Products
  $products_info=[];
  
  //Select Invoice Product Details From Database
  $sql="select * from invoice_products where SID='{$_GET["id"]}'";
  $res=$con->query($sql);
  if($res->num_rows>0){
	  while($row=$res->fetch_assoc()){
		   $products_info[]=[
			"name"=>$row["PNAME"],
			"price"=>$row["PRICE"],
			"qty"=>$row["QTY"],
			"total"=>$row["TOTAL"],
		   ];
	  }
  }
  
  class PDF extends FPDF
  {
    function Header(){

             // Check if it's the first page
             if ($this->page == 1) {
 
      //Display Company Info
      $this->SetFont('Arial','B',14);
      $this->Cell(50,10,"Children on the move",0,1);
      $this->SetFont('Arial','',14);
      $this->Cell(50,7,"1770 Komane Street,",0,1);
      $this->Cell(50,7,"Mini-Munitoria Building",0,1);
      $this->Cell(50,7,"Atteridgeville",0,1);
      $this->Cell(50,7,"0008",0,1);
      
      //Display INVOICE text
      $this->SetY(15);
      $this->SetX(-40);
      $this->SetFont('Arial','B',18);
      $this->Cell(50,10,"REPORT",0,1);
             
      //Display Horizontal line
      $this->Line(0,48,210,48);
      
             }
    }
    
    function body($info,$products_info){
      
      //Billing Details
      $this->SetY(55);
      $this->SetX(10);
      $this->SetFont('Arial','B',12);
      $this->Cell(50,10,"Report authorisation: ",0,1);
      $this->SetFont('Arial','',12);
      $this->Cell(50,7,$info["customer"],0,1);
      $this->Cell(50,7,$info["address"],0,1);
      $this->Cell(50,7,$info["city"],0,1);
      
      //Display Invoice no
      $this->SetY(55);
      $this->SetX(-60);
      $this->Cell(50,7,"Total Income : ".$info["invoice_no"]);
      
      //Display Invoice date
      $this->SetY(63);
      $this->SetX(-60);
      $this->Cell(50,7,"  Date : ".$info["invoice_date"]);
      
      //Display Table headings
      $this->SetY(95);
      $this->SetX(10);
      $this->SetFont('Arial','B',12);
      $this->Cell(80,9,"Operating Expenses",1,0);
      $this->Cell(40,9,"COST",1,0,"C");
      $this->Cell(30,9,"QTY",1,0,"C");
      $this->Cell(40,9,"TOTAL",1,1,"C");
      $this->SetFont('Arial','',12);
      
      //Display table product rows
      foreach($products_info as $row){
        $this->Cell(80,9,$row["name"],"LR",0);
        $this->Cell(40,9,$row["price"],"R",0,"R");
        $this->Cell(30,9,$row["qty"],"R",0,"C");
        $this->Cell(40,9,$row["total"],"R",1,"R");
      }
      //Display table empty rows
      for($i=0;$i<12-count($products_info);$i++)
      {
        $this->Cell(80,9,"","LR",0);
        $this->Cell(40,9,"","R",0,"R");
        $this->Cell(30,9,"","R",0,"C");
        $this->Cell(40,9,"","R",1,"R");
      }
      //Display table total row
      $this->SetFont('Arial','B',12);
      $this->Cell(150,9,"TOTAL in Rand",1,0,"R");
      $this->Cell(40,9,$info["total_amt"],1,1,"R");

      
      //Display amount in words
     /* $this->SetY(225);
      $this->SetX(10);
      $this->SetFont('Arial','B',12);
      $this->Cell(0,9,"Amount in Words ",0,1);
      $this->SetFont('Arial','',12);
      $this->Cell(0,9,$info["words"],0,1);*/
      
    }
    function Footer(){
      
     // Check if it's the first page
     if ($this->page > 1) {

    
      //set footer position
      $this->SetY(-50);
      $this->SetFont('Arial','B',12);
     
       // Check if it's the last page
       if ($this->PageNo() == $this->aliasNbPages()) {
        $this->Cell(0, 10, "Children on the move:", 0, 1, "R");
        $this->Ln(15);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "Authorized Signature", 0, 1, "R");
        $this->SetFont('Arial', '', 10);
    } else {
        // Handle first page separately if needed
        if ($this->PageNo() == 1) {
            $this->Cell(0, 10, "Children on the move:", 0, 1, "R");
            $this->Ln(15);
            $this->SetFont('Arial', '', 12);
            $this->Cell(0, 10, "Authorized Signature", 0, 1, "R");
            $this->SetFont('Arial', '', 10);
        }
    }
  }
      //Display Footer Text
      $this->Cell(0,10,"Quarterly Expenditure Report for Children on the move development Centre.!!",0,1,"C");
      
    }
    
  }
  //Create A4 Page with Portrait 
  $pdf=new PDF("P","mm","A4");
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->body($info,$products_info);
  $pdf->Output();
?>