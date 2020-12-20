<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <title>Application of CFG in Syntactic Parsing</title>
    </head>
    <body class="bg-dark">

        <header>
            <div class="jumbotron jumbotron-fluid bg-dark text-white mb-0">
                <div class="container">
                    <div class="row"><div class="col text-center">
                        <h1 class="display-5 px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">Application of CFG in Syntactic Parsing Of Balinese</h1>
                        <p class="lead">Penerapan Algoritma CYK untuk menentukan suatu kalimat Bahasa Bali valid/invalid</p>
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <p class="flex-sm-fill text-sm-center"></p>
                            <a class="flex-sm-fill text-sm-center nav-link" href="index.php">Identifikasi</a>
                            <a class="flex-sm-fill text-sm-center nav-link active" href="pengujian.php">Pengujian</a>
                            <p class="flex-sm-fill text-sm-center"></p>
                        </nav>
                    </div>
                </div>
            </div>
        </header>
        <div class="jumbotron jumbotron-fluid bg-dark text-white mb-0"></div>

        <div class="container" style="margin-top:-8rem"><div class="row">
            <div class="col-md-12 post-outer"><div class="card"><div class="card-body p-md-5">
                <div class="container">
                    
                    <?php
                        // menghubungkan ke database
                        $conn = mysqli_connect("localhost","root","","cfg");

                        //data di tabel cfg
                        $result1 = mysqli_query($conn,"SELECT * FROM tb_cfg");
                        $result2 = mysqli_query($conn,"SELECT * FROM tb_cfg2");
                        $result3 = mysqli_query($conn,"SELECT * FROM tb_cfg3");
                        $rules = [];
                        while ($row = mysqli_fetch_assoc($result1)) {
                            $rules[] = $row;
                        }
                        while ($row = mysqli_fetch_assoc($result2)) {
                            $rules[] = $row;
                        }
                        while ($row = mysqli_fetch_assoc($result3)) {
                            $rules[] = $row;
                        }
                        
                        //mengambil data di tb_kalimat
                        $result = mysqli_query($conn,"SELECT * FROM tb_kalimat");
                        $data = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $data[] = $row;
                        }

                        foreach($data as $kal){
                            $id = $kal['id'];
                            //mengubah menjadi huruf kecil
                            $kalimat = strtolower($kal['kalimat']);

                            //memecah kalimat menjadi kata dan memasukannya ke dalam array
                            $word = explode(" ", $kalimat);

                            //penerapan CYK
                            //membuat dan deklarasi tabel
                            $x=array();

                            for($i=0; $i< count($word); $i++){
                                for($j=0; $j< count($word); $j++){
                                    $x[$i][$j]="";
                                }
                            }

                            //pengecekan CYK
                            for($i=0; $i<count($word); $i++){
                                for($j=$i; $j<count($word); $j++){
                                    if($i==0){
                                        $has = array();
                                        foreach($rules as $rule){
                                            if($rule['terminal'] == $word[$j]){
                                                $has[] = $rule['variabel'];
                                            }
                                        }
                                        $ter = implode(",", $has);
                                            
                                            $x[$j-$i][$j] = $ter;
                                    }
                                    elseif($i>0){
                                        $cek = array();
                                        for($k=0; $k<$i; $k++){
                                            if($x[$j-$i][($j-$i)+$k] == "" and $x[($j-$i)+($k+1)][$j] != ""){
                                                $y2 = explode(",", $x[($j-$i)+($k+1)][$j]);
                                                foreach($y2 as $v){
                                                    $cek[] = $v;
                                                }
                                            }
                                            elseif($x[($j-$i)+($k+1)][$j] == "" and $x[$j-$i][($j-$i)+$k] != ""){
                                                $y1 = explode(",", $x[$j-$i][($j-$i)+$k]);
                                                foreach($y1 as $v){
                                                    $cek[] = $v;
                                                }
                                            }
                                            elseif($x[$j-$i][($j-$i)+$k] == "" and $x[($j-$i)+($k+1)][$j] == ""){
                                                continue;
                                            }
                                            elseif($x[$j-$i][($j-$i)+$k] != "" and $x[($j-$i)+($k+1)][$j] != ""){
                                                $y1 = explode(",", $x[$j-$i][($j-$i)+$k]);
                                                $y2 = explode(",", $x[($j-$i)+($k+1)][$j]);
                                                for($m=0; $m<count($y1); $m++){
                                                    for($n=0; $n<count($y2); $n++){
                                                        $cek[] = $y1[$m]." ".$y2[$n];
                                                    }
                                                }
                                            }
                                        }
                                        $hasil = array();
                                        if($cek == ""){
                                            $x[$j-$i][$j] = "";
                                        }
                                        else{
                                            $cek2 = array();
                                            $cek2 = array_unique($cek);
                                            foreach($cek2 as $c){
                                                //echo $c."<br>";
                                                foreach($rules as $rule){
                                                    if($c == $rule['terminal']){
                                                        $hasil[] = $rule['variabel'];
                                                    }
                                                    else{
                                                        continue;
                                                    }
                                                }
                                            }
                                        }

                                        if($hasil== ""){
                                            $x[$j-$i][$j] = "";
                                        }
                                        else{
                                            $hasil2 = array();
                                            $hasil2 = array_unique($hasil);
                                            $t = implode(",", $hasil2);
                                            
                                            $x[$j-$i][$j] = $t;
                                            //echo ($j-$i).$j.$t."<br>";;
                                        }
                                    
                                    }
                                }
                            }

                            // //nampilin hasil tabel cyk
                            // echo"<br>";
                            // for($i=0; $i< count($word); $i++){
                            //     for($j=0; $j< count($word); $j++){
                            //         echo $x[$i][$j]."-->";
                            //     }
                            //     echo"<br>";
                            // }
  
                            //ngecek valid atau invalid inputan kalimat dan update database
                            $h = 0;
                            $final = explode(",", $x[0][count($word)-1]);
                            foreach($final as $accept){
                                if ($accept == "Kal"){
                                    mysqli_query($conn,"UPDATE tb_kalimat SET hasil='valid' where id=$id");
                                    $h=1;
                                }
                            }
                            if($h == 0){
                                mysqli_query($conn,"UPDATE tb_kalimat SET hasil='invalid' where id=$id");
                            }

                        }
                        
                        //mengupdate kesimpulan pada tabel tb_kal apakah hasil pengujian benar atau salah
                        foreach($data as $kal){
                            $id1=$kal['id'];
                            $validasi = $kal['validasi'];
                            $hasil_uji = $kal['hasil'];
                            if($validasi==$hasil_uji){
                                mysqli_query($conn,"UPDATE tb_kalimat SET kesimpulan='benar' where id=$id1");
                            }
                            else{
                                mysqli_query($conn,"UPDATE tb_kalimat SET kesimpulan='salah' where id=$id1");
                            }
                        }
                        
                        //menghitung akurasi
                        $benar =  mysqli_query($conn,"SELECT * FROM tb_kalimat WHERE kesimpulan='benar'");
                        $jml_benar = mysqli_num_rows($benar);
                        $jml_data = count($data);
                        $akurasi = round($jml_benar/$jml_data*100,2);
                        //echo "<br><br>Nilai Akurasi = ".$akurasi." %";

                
                    ?>
                    
                    <!--Menampilkan Nilai akurasi-->
                    <h4 class="display-5 px-3 py-3 pt-md-5 pb-md-4 mx-auto text-info mb-3">Nilai Akurasi = <?php echo $akurasi?> %</h4>
                    
                    <!--Menampilkan tabel hasil pengujian-->
                    <table class="table table-hover">
                        <thead>
                            <tr>
                            <th scope="col">No</th>
                            <th scope="col">Kalimat</th>
                            <th scope="col">Diharapkan</th>
                            <th scope="col">Didapat</th>
                            <th scope="col">Kesimpulan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data as $item){?>
                            <tr>
                                <td><?php echo $item['id'] ?></td>
                                <td><?php echo $item['kalimat'] ?></td>
                                <td><?php echo $item['validasi'] ?></td>
                                <td><?php echo $item['hasil'] ?></td>
                                <td><?php echo $item['kesimpulan'] ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <!--B4-->
    </body>
</html>