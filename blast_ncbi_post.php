<?php

// ce code provient d'un template qui a été adapté a notre site
/*
	Industrious by TEMPLATED
	templated.co @templatedco
	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
*/


  error_reporting(0);
  if(isset($_POST['ok-fasta'])){ //
    // Read FASTA sequence from the HTML textbox and encode
    $query = urlencode($_POST["sequence"]);
    $query2= $_POST["sequence"];
  }

  elseif(isset($_POST['ok-file'])) {

    if(!empty($_FILES['file']['tmp_name'])){
      $tmp_file = $_FILES['file']['tmp_name']; //nom temporaire
      $name_file = $_FILES['file']['name']; //nom renseigné par l'utilisateur
      $allowed = array("fa" => "fa"); //formats acceptés
      $content_dir = 'upload/'; // dossier où sera déplacé le fichier

      // Vérifie l'extension du fichier
      $ext = pathinfo($name_file, PATHINFO_EXTENSION);

      if(!array_key_exists($ext, $allowed)){
        ?>
        <div class="col-12">
          <h4><b><center>Veuillez sélectionner un fichier au format fasta</center></b></h4>
       </div>
       <?php
      }
      elseif(!is_uploaded_file($tmp_file)){
          $photo_valide=false;
          ?>
          <div class="col-12">
            <h4><b><center>erreur chargement fichier fasta</center></b></h4>
         </div>
         <?php
      }
      // on copie le fichier dans le dossier de destination
      elseif(!move_uploaded_file($tmp_file, $content_dir . $name_file)){
          ?>
          <div class="col-12">
            <h4><b><center>erreur chargement fichier fasta</center></b></h4>
         </div>
         <?php
      }
      else{ //ça marche
        function fas_read($file){
          $query = '';
          $handle = fopen($file, "r");
          if ($handle) {
            while (($line = fgets($handle)) !== false) {
              $query .= $line;
            }
            fclose($handle);
          }
          return $query;
        }
        $query = fas_read($content_dir . $name_file);
        $query2 = $query;
        $query =  urlencode($query);

      }
    }
  }

  if(empty($query)){
    ?>
    <div class="col-12">
      <h4><b><center>Veuillez renseigner les champs</center></b></h4>
      <p> Redirection vers la page du blast. </p>
    </div>
    <?php
    header('Refresh: 2; blast_ncbi.php');
  }
  else{
    $banque = $_POST['banque'];
    $program = NULL;

    if($banque=='pdb' || $banque=='nr'|| $banque=='refseq_protein' || $banque=='swissprot' ){
      $program='blastp';
    }
    elseif($banque=='localadn'){
      file_put_contents('./data/blast/in.fa', $query2);
      exec('/usr/local/ncbi/blast/bin/blastn -query ./data/blast/in.fa -db ./data/cds-database/cds-database -outfmt 5 -out ./data/blast/out.xml 2>&1');
      $xml = simplexml_load_file("./data/blast/out.xml") or die("Error: Cannot able to create object");
    }
    elseif($banque=='localprot'){
      file_put_contents('./Data/blast/in.fa', $query2);
      exec('/usr/local/ncbi/blast/bin/blastp -query ./data/blast/in.fa -db ./data/prot-database/prot-database.fa -outfmt 5 -out ./data/blast/out.xml 2>&1');
      $xml = simplexml_load_file("./data/blast/out.xml") or die("Error: Cannot able to create object");
    }
    else{
      $program='blastn';
    }
    // Construction de la demande a BLASTs
    //$query = 'MKRISTTITTTITITTGNGAG';
    //$banque = 'xxx';
    //$program = 'blastp';
    if($program!=NULL){
    $data = array('CMD' => 'Put',  'PROGRAM' => $program,  'DATABASE' => $banque, 'QUERY' => $query);
    $options = array(
      'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
       )
    );
    $context  = stream_context_create($options);

    // Recupere la reponse de BLAST
    $result = file_get_contents("https://blast.ncbi.nlm.nih.gov/blast/Blast.cgi", false, $context);

    // Recupere le Request ID
    preg_match("/^.*RID = .*\$/m", $result, $ridm);
    $rid = implode("\n", $ridm);
    $rid = preg_replace('/\s+/', '', $rid);
    $rid = str_replace("RID=", "", $rid);

    // Recupere le RTOE
    preg_match("/^.*RTOE = .*\$/m", $result, $rtoem);
    $rtoe = implode("\n", $rtoem);
    $rtoe = preg_replace('/\s+/', '', $rtoe);
    $rtoe = str_replace("RTOE=", "", $rtoe);

    // Poll for results
    while(true) {

      sleep(10);

      $opts = array('http' => array('method' => 'GET'));

      $contxt = stream_context_create($opts);
      $reslt = file_get_contents("https://blast.ncbi.nlm.nih.gov/blast/Blast.cgi?CMD=Get&FORMAT_OBJECT=SearchInfo&RID=$rid", false, $contxt);

      if (preg_match('/Status=WAITING/', $reslt)) {
        continue;
      }

      if (preg_match('/Status=FAILED/', $reslt)) {
        ?>
        <div class="col-12">
          <h4><b><center>La recherche <?php echo $rid?> a échoué, écrivez à blast-help\@ncbi.nlm.nih.gov.</center></b></h4>
          <a href="blast_ncbi.php"><b>Retour sur la page du blast.</b></a>
        </div>
        <?php
        exit(4);
      }

      if (preg_match('/Status=UNKNOWN/', $reslt)) {
        ?>
        <div class="col-12">
          <h4><b><center>La recherche <?php echo $rid?> a expiré.  Verifiez que vous avez bien rentré tous les champs correctement (erreur commune : cohérence type de recherche et séquence renseignée)</center></b></h4>
          <a href="blast_ncbi.php"><b>Retour sur la page du blast.</b></a>
        </div>
        <?php
        exit(3);
      }

      if (preg_match('/Status=READY/', $reslt)) {
        if(preg_match('/ThereAreHits=yes/', $reslt)) {
          ?>
          <div class="col-12">
            <h4><b><center>recherche réussie</center></b></h4>
          </div>
          <?php
          break;
        }
        else {
          ?>
          <div class="col-12">
            <h4><b><center>Aucun hit trouvé</center></b></h4>
            <a href="blast_ncbi.php"><b>Retour sur la page du blast.</b></a>
          </div>
          <?php
          exit(2);
        }
      }

      // If we get here, something unexpected happened.
      exit(5);

    } // End poll loop

    // Retrieve and display results
    $xml = simplexml_load_file("https://blast.ncbi.nlm.nih.gov/blast/Blast.cgi?CMD=Get&FORMAT_TYPE=XML&RID=$rid") or die("Error: Cannot able to create object");
  }?>


    <div class="row">
      <div class="col-12">
        <h2> Résultats </h2>
        <?php if ($program!=NULL){ ?>
          <a href="https://blast.ncbi.nlm.nih.gov/blast/Blast.cgi?CMD=Get&FORMAT_TYPE=Text&RID=<?php echo $rid?>"
          download="result_<?php echo $rid?>.txt">Télécharger le résultat</a>
        <?php } ?>
        <br>
        <a href="blast_ncbi.php"><b>Retour sur la page de recherche.</b></a>
      </div>

      <?php
        function def_split($x, $y) {
          $a = "&gt;" . $x . " " . $y;
          $a = preg_replace("/\>/", "&gt;", $a);
          $a = preg_replace("/ \&gt\;/", "<br/>&gt;", $a);
          return $a;
        }
        function def_trim($def) {
          $defn = preg_replace("/ \&gt\;/", ">", $def);
          $defn = explode('>', $defn);
          if (strlen($defn[0]) > 65) return substr($defn[0], 0, 63) . "...";
          else return $defn[0];
        }
        function fmtprint($length, $query_seq, $query_seq_from, $query_seq_to, $align_seq, $sbjct_seq, $sbjct_seq_from, $sbjct_seq_to) {
          $n = (int)($length / 60);
          $r = $length % 60;
          if ($r > 0) $t = $n + 1;
          else $t = $n;
          $j = 0;
          $xn = $query_seq_from;
          $an = $sbjct_seq_from;
          for ($i = 0; $i < $t; $i++) {
            $xs = substr($query_seq, 60*$i, 60);
            $xs = preg_replace("/-/", "", $xs);
            $yn = $xn + strlen($xs) - 1;
            printf("\nQuery  %-4d %s  %d", $xn, substr($query_seq, 60*$i, 60), $yn);
            $xn = $yn + 1;
            printf("\n            %s", substr($align_seq, 60*$i, 60));
            $ys = substr($sbjct_seq, 60*$i, 60);
            $ys = preg_replace("/-/", "", $ys);
            $bn = $an + strlen($ys) - 1;
            printf("\nSbjct  %-4d %s  %d\n", $an, substr($sbjct_seq, 60*$i, 60), $bn);
            $an = $bn + 1;
          }
        }
        function annotate($def) {
          $pn = preg_match_all('/\|pdb\|\K[^\|]*(?=\|)/', $def, $m);
          if ($pn > 0) {
            for ($i1 = 0; $i1 < $pn; $i1++) {
              $id[$i1] = $m[0][$i1];
            }
            $id = array_unique($id);
            $id = array_filter($id);
            $id = array_values($id);
            if (!empty($id)) {
              $n = count($id);
              for ($i1 = 0; $i1 < $n; $i1++) {
                $def = preg_replace("/$id[$i1]/", "<a href=\"http://www.rcsb.org/pdb/explore/explore.do?structureId=$id[$i1]\" id='ilnk' target='_blank'>". $id[$i1] . "</a>", $def);
              }
            }
          }
          $gn = preg_match_all('/gi\|\K[^\|]*(?=\|)/', $def, $m1);
          if ($gn > 0) {
            for ($i2 = 0; $i2 < $gn; $i2++) {
              $gid[$i2] = $m1[0][$i2];
            }
            $gid = array_unique($gid);
            $gid = array_filter($gid);
            $gid = array_values($gid);
            if (!empty($gid)) {
              $n1 = count($gid);
              for ($i2 = 0; $i2 < $n1; $i2++) {
                $def = preg_replace("/$gid[$i2]/", "<a href=\"https://www.ncbi.nlm.nih.gov/protein/$gid[$i2]\" id='ilnk' target='_blank'>". $gid[$i2] . "</a>", $def);
              }
            }
          }
          $gb = preg_match_all('/gb\|\K[^\|]*(?=\|)/', $def, $m2);
          if ($gb > 0) {
            for ($i3 = 0; $i3 < $gb; $i3++) {
              $gbid[$i3] = $m2[0][$i3];
            }
            $gbid = array_unique($gbid);
            $gbid = array_filter($gbid);
            $gbid = array_values($gbid);
            if (!empty($gbid)) {
              $n2 = count($gbid);
              for ($i3 = 0; $i3 < $n2; $i3++) {
                $def = preg_replace("/$gbid[$i3]/", "<a href=\"https://www.ncbi.nlm.nih.gov/nucleotide/$gbid[$i3]\" id='ilnk' target='_blank'>". $gbid[$i3] . "</a>", $def);
              }
            }
          }
          $rf = preg_match_all('/ref\|\K[^\|]*(?=\|)/', $def, $m3);
          if ($rf > 0) {
            for ($i4 = 0; $i4 < $rf; $i4++) {
              $rfid[$i4] = $m3[0][$i4];
            }
            $rfid = array_unique($rfid);
            $rfid = array_filter($rfid);
            $rfid = array_values($rfid);
            if (!empty($rfid)) {
              $n3 = count($rfid);
              for ($i4 = 0; $i4 < $n3; $i4++) {
                $def = preg_replace("/$rfid[$i4]/", "<a href=\"https://www.ncbi.nlm.nih.gov/nuccore/$rfid[$i4]\" id='ilnk' target='_blank'>". $rfid[$i4] . "</a>", $def);
              }
            }
          }
          $sp = preg_match_all('/sp\|\K[^\|]*(?=\|)/', $def, $m4);
          if ($sp > 0) {
            for ($i5 = 0; $i5 < $sp; $i5++) {
              $spid[$i5] = $m4[0][$i5];
            }
            $spid = array_unique($spid);
            $spid = array_filter($spid);
            $spid = array_values($spid);
            if (!empty($spid)) {
              $n4 = count($spid);
              for ($i5 = 0; $i5 < $n4; $i5++) {
                $def = preg_replace("/$spid[$i5]/", "<a href=\"http://www.uniprot.org/uniprot/" . array_shift(explode('.', $spid[$i5])) . "\" id='ilnk' target='_blank'>". $spid[$i5] . "</a>", $def);
              }
            }
          }
          return $def;
        }
      ?>
      <div class="col-6">
        <table align="center">
          <tbody>
            <tr><td>Program</td><td><?php print $xml->BlastOutput_program; ?></td></tr>
            <tr><td>Version</td><td><?php print $xml->BlastOutput_version; ?></td></tr>
            <tr><td>Reference</td><td><?php print $xml->BlastOutput_reference; ?></td></tr>
            <tr><td>Database</td><td><?php echo $banque?></td></tr>
            <tr><td>Query ID</td><td><?php print $xml->{'BlastOutput_query-ID'}; ?></td></tr>
            <tr><td>Definition</td><td><?php print $xml->{'BlastOutput_query-def'}; ?></td></tr>
            <tr><td>Length</td><td><?php print $xml->{'BlastOutput_query-len'}; ?></td></tr>
            <tr><td>Matrix</td><td><?php print $xml->BlastOutput_param->Parameters->Parameters_matrix; ?></td></tr>
            <tr><td>E-value</td><td><?php print $xml->BlastOutput_param->Parameters->Parameters_expect; ?></td></tr>
            <tr><td>Gap Open</td><td><?php print $xml->BlastOutput_param->Parameters->{'Parameters_gap-open'}; ?></td></tr>
            <tr><td>Gap Extend</td><td><?php print $xml->BlastOutput_param->Parameters->{'Parameters_gap-extend'}; ?></td></tr>
            <tr><td>Filter</td><td><?php print $xml->BlastOutput_param->Parameters->Parameters_filter; ?></td></tr>
          </tbody>
        </table>
      </div>

      <?php
      foreach($xml->BlastOutput_iterations->Iteration as $itr) {
        $Iteration_iter_num = $itr->{'Iteration_iter-num'};
        $Iteration_query_ID = $itr->{'Iteration_query-ID'};
        $Iteration_query_def = $itr->{'Iteration_query-def'};
        $Iteration_query_len = $itr->{'Iteration_query-len'};
      ?>
      <div class="col-6">
        <table class="alt">
          <tbody>
            <tr><td>Number of Sequences</td><td><?php print $itr->Iteration_stat->Statistics->{'Statistics_db-num'}; ?></td></tr>
            <tr><td>Length of database</td><td><?php print $itr->Iteration_stat->Statistics->{'Statistics_db-len'}; ?></td></tr>
            <tr><td>Length adjustment</td><td><?php print $itr->Iteration_stat->Statistics->{'Statistics_hsp-len'}; ?></td></tr>
            <tr><td>Effective search space</td><td><?php print $itr->Iteration_stat->Statistics->{'Statistics_eff-space'}; ?></td></tr>
            <tr><td>Kappa (&kappa;)</td><td><?php print $itr->Iteration_stat->Statistics->{'Statistics_kappa'}; ?></td></tr>
            <tr><td>Lambda (&lambda;)</td><td><?php print $itr->Iteration_stat->Statistics->{'Statistics_lambda'}; ?></td></tr>
            <tr><td>Entropy (H)</td><td><?php print $itr->Iteration_stat->Statistics->{'Statistics_entropy'}; ?></td></tr>
          </tbody>
        </table>
      </div>
      <div class="col-12">
  	    <table align="center">
  		    <tbody>
            <tr>
              <th colspan="2"><h3>Sequences produisant un alignement significatif<h3></th>
              <th>Score<br/>(Bits)</th>
              <th>E<br/>Value</th>
              <th>%<br/>Alignement</th>
            </tr>

            <?php
              foreach($itr->Iteration_hits->Hit as $lst) {
                $Hit_def = $lst->Hit_def;
                $Hit_accession = $lst->Hit_accession;
                $Hsp_bit_score = $lst->Hit_hsps->Hsp->{'Hsp_bit-score'};
                $Hsp_evalue = $lst->Hit_hsps->Hsp->Hsp_evalue;
                $Hsp_align_len = $lst->Hit_hsps->Hsp->{'Hsp_align-len'};
                $Hsp_identity = $lst->Hit_hsps->Hsp->{'Hsp_identity'};

                if($program==NULL){
                  $req_renseigne = $bdd->prepare('SELECT O.souche, O.nom_orga, G.* FROM GENE AS G, CHROMOSOME AS C, ORGANISME AS O WHERE id_seq = :id_seq AND G.id_chromosome = C.nom_chromosome AND C.id_orga = O.id_orga');
                  $req_renseigne->execute(array(
                      'id_seq' => $Hit_accession));
                  $resultat_renseigne = $req_renseigne->fetch(); ?>
                  <tr>
                    <td align="center"><?php print "<a href='#" . $Hit_accession . "' id='ilnk'>" . $Hit_accession . "</a>"; ?></td>
                    <td><?php echo($resultat_renseigne["nom_orga"]." (".$resultat_renseigne["souche"].") ".$resultat_renseigne["description"])?></td>
                    <td align="center"><?php print (int)$Hsp_bit_score; ?></td>
                    <td align="center"><?php printf("%.1f", $Hsp_evalue); ?></td>
                    <td align="center"><?php print((int)(($Hsp_identity/$Hsp_align_len)*100)); ?></td>
                  </tr>
                  <?php } else{ ?>
                    <tr>
                      <td align="center"><?php print "<a href='#" . $Hit_accession . "' id='ilnk'>" . $Hit_accession . "</a>"; ?></td>
                      <td><?php print def_trim($Hit_def); ?></td>
                      <td align="center"><?php print (int)$Hsp_bit_score; ?></td>
                      <td align="center"><?php printf("%.1f", $Hsp_evalue); ?></td>
                      <td align="center"><?php print((int)(($Hsp_identity/$Hsp_align_len)*100)); ?></td>
                    </tr>
                  <?php }
              }
            ?>
  		    </tbody>
  	    </table>
      </div>
      <?php
        foreach($itr->Iteration_hits->Hit as $algn) {
          $Hit_num = $algn->Hit_num;
          $Hit_id = $algn->Hit_id;
          $Hit_def = $algn->Hit_def;
          $Hit_accession = $algn->Hit_accession;
          $Hit_len = $algn->Hit_len;
          $Hsp_num = $algn->Hit_hsps->Hsp->Hsp_num;
          $Hsp_bit_score = $algn->Hit_hsps->Hsp->{'Hsp_bit-score'};
          $Hsp_score = $algn->Hit_hsps->Hsp->Hsp_score;
          $Hsp_evalue = $algn->Hit_hsps->Hsp->Hsp_evalue;
          $Hsp_query_from = $algn->Hit_hsps->Hsp->{'Hsp_query-from'};
          $Hsp_query_to = $algn->Hit_hsps->Hsp->{'Hsp_query-to'};
          $Hsp_hit_from = $algn->Hit_hsps->Hsp->{'Hsp_hit-from'};
          $Hsp_hit_to = $algn->Hit_hsps->Hsp->{'Hsp_hit-to'};
          $Hsp_query_frame = $algn->Hit_hsps->Hsp->{'Hsp_query-frame'};
          $Hsp_hit_frame = $algn->Hit_hsps->Hsp->{'Hsp_hit-frame'};
          $Hsp_identity = $algn->Hit_hsps->Hsp->{'Hsp_identity'};
          $Hsp_positive = $algn->Hit_hsps->Hsp->{'Hsp_positive'};
          $Hsp_gaps = $algn->Hit_hsps->Hsp->{'Hsp_gaps'};
          $Hsp_align_len = $algn->Hit_hsps->Hsp->{'Hsp_align-len'};
          $Hsp_qseq = $algn->Hit_hsps->Hsp->{'Hsp_qseq'};
          $Hsp_midline = $algn->Hit_hsps->Hsp->{'Hsp_midline'};
          $Hsp_hseq = $algn->Hit_hsps->Hsp->{'Hsp_hseq'};
          ?>
          <div class="col-12">
          <table >
            <tbody>
              <?php if($program==NULL){
                $req_renseigne = $bdd->prepare('SELECT O.souche, O.nom_orga, G.* FROM GENE AS G, CHROMOSOME AS C, ORGANISME AS O WHERE id_seq = :id_seq AND G.id_chromosome = C.nom_chromosome AND C.id_orga = O.id_orga');
                $req_renseigne->execute(array(
                    'id_seq' => $Hit_accession));
                $resultat_renseigne = $req_renseigne->fetch();
                ?>
                <tr><th><?php print "Hit Number: " . $Hit_num . ", Accession Number: <span id='" . $Hit_accession . "'>" . "<a href='profil_gene.php?id_seq=$Hit_accession'>$Hit_accession</a>"; ?></span></th></tr>
                <tr><td><a href="profil_gene.php?id_seq=<?php echo $Hit_accession ?>"><?php echo $Hit_accession ?></a><?php echo(" ".$resultat_renseigne["nom_orga"]." (".$resultat_renseigne["souche"].") ".$resultat_renseigne["description"])?></td><tr>
                <tr><td><?php print "Length = ". $Hit_len . ", Score =  " . (int)$Hsp_bit_score . " bits (" . $Hsp_score . "), Expect = " . $Hsp_evalue . ",<br>Identities = " . $Hsp_identity . "/" . $Hsp_align_len . " (" . (int)(($Hsp_identity/$Hsp_align_len)*100) . "%), Positives = " . $Hsp_positive . "/" . $Hsp_align_len . " (" . (int)(($Hsp_positive/$Hsp_align_len)*100) . "%), Gaps = ". $Hsp_gaps . "/" . $Hsp_align_len . " (" . (int)(($Hsp_gaps/$Hsp_align_len)*100) . "%)"; ?></td></tr>
                <tr><td><pre style="line-height: 130%;"><?php fmtprint($Hsp_align_len, $Hsp_qseq, $Hsp_query_from, $Hsp_query_to, $Hsp_midline, $Hsp_hseq, $Hsp_hit_from, $Hsp_hit_to); ?></pre></td></tr>

              <?php } elseif($banque=='pdb') { ?>
                <tr><th><?php print "Hit Number: " . $Hit_num . ", Accession Number: <span id='" . $Hit_accession . "'>" . $Hit_accession; ?></span></th></tr>
                <tr><td><?php $sdef = def_split($Hit_id, $Hit_def); print annotate($sdef); ?></td></tr>
                <tr><td><?php print "Length = ". $Hit_len . ", Score =  " . (int)$Hsp_bit_score . " bits (" . $Hsp_score . "), Expect = " . $Hsp_evalue . ",<br>Identities = " . $Hsp_identity . "/" . $Hsp_align_len . " (" . (int)(($Hsp_identity/$Hsp_align_len)*100) . "%), Positives = " . $Hsp_positive . "/" . $Hsp_align_len . " (" . (int)(($Hsp_positive/$Hsp_align_len)*100) . "%), Gaps = ". $Hsp_gaps . "/" . $Hsp_align_len . " (" . (int)(($Hsp_gaps/$Hsp_align_len)*100) . "%)"; ?></td></tr>
                <tr><td><pre style="line-height: 130%;"><?php fmtprint($Hsp_align_len, $Hsp_qseq, $Hsp_query_from, $Hsp_query_to, $Hsp_midline, $Hsp_hseq, $Hsp_hit_from, $Hsp_hit_to); ?></pre></td></tr>
            <?php }
              else{ ?>
                <tr><th><?php print "Hit Number: " . $Hit_num . ", Accession Number: <span id='" . $Hit_accession . "'>" . $Hit_accession; ?></span></th></tr>
                <tr><td><?php $sdef = def_split($Hit_id, $Hit_def); print annotate($sdef); ?></td></tr>
                <tr><td><?php print "Length = ". $Hit_len . ", Score =  " . (int)$Hsp_bit_score . " bits (" . $Hsp_score . "), Expect = " . $Hsp_evalue . ",<br>Identities = " . $Hsp_identity . "/" . $Hsp_align_len . " (" . (int)(($Hsp_identity/$Hsp_align_len)*100) . "%), Positives = " . $Hsp_positive . "/" . $Hsp_align_len . " (" . (int)(($Hsp_positive/$Hsp_align_len)*100) . "%), Gaps = ". $Hsp_gaps . "/" . $Hsp_align_len . " (" . (int)(($Hsp_gaps/$Hsp_align_len)*100) . "%)"; ?></td></tr>
                <tr><td><pre style="line-height: 130%;"><?php fmtprint($Hsp_align_len, $Hsp_qseq, $Hsp_query_from, $Hsp_query_to, $Hsp_midline, $Hsp_hseq, $Hsp_hit_from, $Hsp_hit_to); ?></pre></td></tr>

            <?php  }?>
            </tbody>
          </table>
        </div>
  	  <?php
  	    }
  	  ?>

      <?php
        }
      ?>
  </div>
  <?php
  }
  ?>
