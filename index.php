<?php require "config.php"; ?>
<html>
<head>
    <title>CHILDREN ON THE MOVE</title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    <link rel='stylesheet' href='https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css'>
    <script src="https://code.jquery.com/ui/1.13.0-rc.3/jquery-ui.min.js" integrity="sha256-R6eRO29lbCyPGfninb/kjIXeRjMOqY3VWPVk6gMhREk=" crossorigin="anonymous"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .card {
            margin: 20px 0;
            max-width: 100%; /* Ensure the card uses full width available */
            margin: 20px auto; /* Center the card */
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 16px;
            text-align: center;
            padding: 10px;
        }
        .card-body {
            padding: 10px;
        }
        .card-body .form-control {
            font-size: 14px;
            padding: 5px;
        }
        .card-footer {
            margin-top: 10px;
        }
        .chart-container {
            position: relative;
            height: 300px; /* Set a fixed height for charts */
            width: 100%;
        }
        .chart-col {
            padding: 15px;
        }
        .form-control {
            font-size: 14px;
            padding: 5px;
        }
        .percentage-summary {
            height: 300px; /* Set the same height as charts for consistency */
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class='container pt-5'>
        <h1 class='text-center text-primary'>Children on the Move Development Centre Report per Quarter</h1><hr>
        <!-- Chart Containers -->
         <div class="row">
            <div class="col-md-4 chart-col">
                <div class="card">
                    <div class="card-header">
                        Line Graph
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 chart-col">
                <div class="card">
                    <div class="card-header">
                        Pie Chart
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
            if(isset($_POST["submit"])){
                $invoice_no=$_POST["invoice_no"];
                 $invoice_date=date("Y-m-d",strtotime($_POST["invoice_date"]));
                $cname=mysqli_real_escape_string($con,$_POST["cname"]);
                $caddress=mysqli_real_escape_string($con,$_POST["caddress"]);
                $ccity=mysqli_real_escape_string($con,$_POST["ccity"]);
                $grand_total=mysqli_real_escape_string($con,$_POST["grand_total"]);
                
                $sql = "INSERT INTO invoice (INVOICE_NO, INVOICE_DATE, CNAME, CADDRESS, CCITY, GRAND_TOTAL) VALUES ('{$invoice_no}', '{$invoice_date}', '{$cname}', '{$caddress}', '{$ccity}', '{$grand_total}')";
                if($con->query($sql)){
                    $sid = $con->insert_id;
                    
                    $sql2 = "INSERT INTO invoice_products (SID, PNAME, PRICE, QTY, TOTAL) VALUES ";
                    $rows = [];
                    for($i = 0; $i < count($_POST["pname"]); $i++) {
                        $pname = mysqli_real_escape_string($con, $_POST["pname"][$i]);
                        $price = mysqli_real_escape_string($con, $_POST["price"][$i]);
                        $qty = mysqli_real_escape_string($con, $_POST["qty"][$i]);
                        $total = mysqli_real_escape_string($con, $_POST["total"][$i]);
                        $rows[] = "('{$sid}', '{$pname}', '{$price}', '{$qty}', '{$total}')";
                    }
                    $sql2 .= implode(",", $rows);
                    if($con->query($sql2)){
                        echo "<div class='alert alert-success'>Invoice Added. <a href='print.php?id={$sid}' target='_BLANK'>Click</a> here to Print Invoice</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Invoice Addition Failed.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Invoice Addition Failed.</div>";
                }
            }
        ?>

        <form method='post' action='index.php' autocomplete='off'>
            <div class='row'>
                <div class='col-md-4'>
                    <h5 class='text-success'>Quarterly Expenditure Detail</h5>
                    <div class='form-group'>
                        <label>Total Income</label>
                        <input type='number' step='0.01' name='invoice_no' required class='form-control'>
                    </div>
                    <div class='form-group'>
                        <label>Date</label>
                        <input type='text' name='invoice_date' id='date' required class='form-control'>
                    </div>
                </div>
                <div class='col-md-8'>
                    <h5 class='text-success'>Manager</h5>
                    <div class='form-group'>
                        <label>Name and Surname</label>
                        <input type='text' name='cname' required class='form-control'>
                    </div>
                    <div class='form-group'>
                        <label>Designation</label>
                        <input type='text' name='caddress' required class='form-control'>
                    </div>
                    <div class='form-group'>
                        <label>City</label>
                        <input type='text' name='ccity' required class='form-control'>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-12'>
                    <h5 class='text-success'>Expenses Details</h5>
                    <table class='table table-bordered'>
                        <thead>
                            <tr>
                                <th>Operating Expenses</th>
                                <th>Cost</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id='product_tbody'>
                            <tr>
                                <td><input type='text' required name='pname[]' class='form-control'></td>
                                <td><input type='number' step='0.01' required name='price[]' class='form-control price'></td>
                                <td><input type='number' required name='qty[]' class='form-control qty'></td>
                                <td><input type='number' step='0.01' required name='total[]' class='form-control total'></td>
                                <td><input type='button' value='x' class='btn btn-danger btn-sm btn-row-remove'></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><input type='button' value='+ Add Row' class='btn btn-primary btn-sm' id='btn-add-row'></td>
                                <td colspan='2' class='text-right'>Total</td>
                                <td><input type='text' name='grand_total' id='grand_total' class='form-control' required readonly></td>
                            </tr>
                        </tfoot>
                    </table>
                    <!-- Card for percentage and remainder -->
                    <div class="card">
                        <div class="card-header">
                            Summary
                        </div>
                        <div class="card-body">
                            <table class='table'>
                                <tbody>
                                    <tr>
                                        <td class='text-right'>Total Income</td>
                                        <td><input type='number' step='0.01' name='total_income' id='total_income' class='form-control' required></td>
                                    </tr>
                                    <tr>
                                        <td class='text-right'>Remainder</td>
                                        <td><input type='text' id='remainder' class='form-control' readonly></td>
                                    </tr>
									<tr>
                                        <td class='text-right'>Percentage of Total Income</td>
                                        <td><input type='text' id='percentage' class='form-control' readonly></td>
                                   </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <input type='submit' name='submit' value='Save Invoice' class='btn btn-success float-right'>
                </div>
            </div>
        </form>
    </div>
    
    <script>
        $(document).ready(function(){
            $("#date").datepicker({
                dateFormat: "dd-mm-yy"
            });

            $("#btn-add-row").click(function(){
                var row = "<tr> <td><input type='text' required name='pname[]' class='form-control'></td> <td><input type='number' step='0.01' required name='price[]' class='form-control price'></td> <td><input type='number' required name='qty[]' class='form-control qty'></td> <td><input type='number' step='0.01' required name='total[]' class='form-control total'></td> <td><input type='button' value='x' class='btn btn-danger btn-sm btn-row-remove'></td> </tr>";
                $("#product_tbody").append(row);
            });

            $("body").on("click", ".btn-row-remove", function(){
                if(confirm("Are You Sure?")){
                    $(this).closest("tr").remove();
                    grand_total();
                    calculate_remainder();
                }
            });

            $("body").on("keyup", ".price", function(){
                var price = Number($(this).val());
                var qty = Number($(this).closest("tr").find(".qty").val());
                $(this).closest("tr").find(".total").val(price * qty);
                grand_total();
                calculate_remainder();
            });

            $("body").on("keyup", ".qty", function(){
                var qty = Number($(this).val());
                var price = Number($(this).closest("tr").find(".price").val());
                $(this).closest("tr").find(".total").val(price * qty);
                grand_total();
                calculate_remainder();
            });

            function grand_total(){
                var tot = 0;
                $(".total").each(function(){
                    tot += Number($(this).val());
                });
                $("#grand_total").val(tot.toFixed(2));
                calculate_remainder();
            }

            function calculate_remainder() {
                var total_income = Number($("#total_income").val());
                var grand_total = Number($("#grand_total").val());
                var remainder = total_income - grand_total;

                // Calculate the percentage of the grand_total relative to total_income
                var percentage = (grand_total / total_income) * 100;

                // Round the percentage to 0 decimal places
                percentage = Math.round(percentage);

                $("#remainder").val(remainder.toFixed(2));
                $("#percentage").val(percentage + "%");
            }

            // Initialize Line Chart
            var ctxLine = document.getElementById('lineChart').getContext('2d');
            var lineChart = new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                    datasets: [{
                        label: 'Monthly Expenditure',
                        data: [12, 19, 3, 5, 2, 3],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Initialize Pie Chart
            var ctxPie = document.getElementById('pieChart').getContext('2d');
            var pieChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: ['Rent', 'Utilities', 'Salaries', 'Miscellaneous'],
                    datasets: [{
                        label: 'Expense Distribution',
                        data: [30, 20, 25, 25],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                }
            });
        });
    </script>
</body>
</html>
