let nData = -1;
let totalPayment = 0;

ChangeKate = function()
{
	let jenis = $("#kate").val();
	let dept = $("#departemen").val();
	
	$.ajax({
        type: "POST",
        url: "multitrans2.content.changekate.php",
        data: "jenis="+jenis+"&departemen="+dept,
        success: function(response) {
			$("#divPaymentInfo").html("");
			$("#divSelectPayment").html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {
			$("#divSelectPayment").html("ERROR: " + xhr.responseText);
            Logger.HandleError(xhr.responseText);
        }
	});
};

ChangePayment = function()
{
	let idpayment = parseInt($("#payment").val());
	if (idpayment === 0)
	{
		$("#divPaymentInfo").html("");
		return;
	}
	
	let jenis = $("#kate").val();
	let dept = $("#departemen").val();
	let noid = $("#noid").val();
	let kelompok = $("#kelompok").val();
	let idtahunbuku = $("#idtahunbuku").val();

	$.ajax({
        type: "POST",
        url: "multitrans2.content.paymentinfo.php",
        data: "jenis="+jenis+"&departemen="+dept+"&noid="+noid+"&idtahunbuku="+idtahunbuku+"&kelompok="+kelompok+"&idpayment="+idpayment,
        success: function(response)
		{
			$("#divPaymentInfo").html(response).hide().fadeIn(300);
			$("#divPaymentInfoContainer").scrollTop($("#divPaymentInfoContainer")[0].scrollHeight);

			if (jenis === "JTT" || jenis === "CSWJB")
			{
				let idbesarjtt = parseInt($("#idbesarjtt").val());
				if (idbesarjtt === 0)
					$("#tagihan").focus();
				else
                    $("#jcicilan").focus();
			}
			else
			{
				$("#jumlah").focus();
			}
        },
        error: function(xhr)
		{
			$("#divPaymentInfo").html("ERROR: " + xhr.responseText);
            Logger.HandleError(xhr.responseText);
        }
	});
}

CalculatePay = function()
{
	let cicilan = $("#jcicilan").val();
	let diskon = $("#jdiskon").val();
	cicilan = rupiahToNumber(cicilan);
	diskon = rupiahToNumber(diskon);
	
	let bayar = cicilan - diskon;
	$("#jbayar").val(numberToRupiah(bayar));
};

GetRupiahValueOf = function(id)
{
	return $.trim(rupiahToNumber($("#" + id).val()));
};

ValidateRupiahInput = function(id, name, allowEmpty)
{
	let input = GetRupiahValueOf(id);
	if (!allowEmpty && input.length == 0)
	{
		alert("Anda belum memasukkan " + name + "!");
		Validator.FocusErrorById(id);
		return false;
	}
	
	if ( isNaN(input) )
	{
		alert(name + " harus angka!");
		Validator.FocusErrorById(id);
		return false;
	}
	
	if (parseInt(input) < 0)
	{
		alert(name + " harus positif atau sama dengan nol!");
		Validator.FocusErrorById(id);
		return false;
	}
	
	return true;
}

DeletePayment = function(rowno)
{
	if (!confirm("Hapus data ini?"))
		return;
	
	let jcicilan = parseInt($("#i_jcicilan_" + rowno).val());
	let jdiskon = parseInt($("#i_jdiskon_" + rowno).val());
	
	//alert(totalPayment + " vs " + jcicilan + " - " + jdiskon)
	
	totalPayment -= jcicilan - jdiskon;
	$("#spanTotalInfo").text(numberToRupiah(totalPayment));
	
	$("#row" + rowno).remove();
	$("#flagrow" + rowno).val(0);
};

AddToPaymentListWjb = function()
{
	let kate = $("#kate").val();
	let idpayment = $("#payment").val();
	if ($("#" + kate + "_" + idpayment).length > 0)
	{
		alert("Jenis pembayaran ini sudah ada dalam Daftar Pembayaran!");
		return false;
	}
	
	let isok = true;
	let idbesarjtt = parseInt($("#idbesarjtt").val());
	if (idbesarjtt === 0)
	{
		isok = ValidateRupiahInput("tagihan", "Total Tagihan", false) &&
			   ValidateRupiahInput("bcicilan", "Besar Cicilan", false);
	}
	
	if (!isok)
		return false;
	
	isok = ValidateRupiahInput("jcicilan", "Pembayaran Cicilan", false) &&
		   ValidateRupiahInput("jdiskon", "Jumlah Diskon", true);
	
	if (!isok)
		return false;
	
	let tagihan = parseInt(GetRupiahValueOf("tagihan"));
	let bcicilan = parseInt(GetRupiahValueOf("bcicilan"));
	let jcicilan = parseInt(GetRupiahValueOf("jcicilan"));
	let jdiskon = GetRupiahValueOf("jdiskon");
	jdiskon = jdiskon.length === 0 ? 0 : parseInt(jdiskon);
	
	let sisa = tagihan;
	if (idbesarjtt !== 0)
		sisa = parseInt(GetRupiahValueOf("sisa"));

	if (jcicilan < jdiskon)
	{
		alert("Jumlah pembayaran harus lebih besar daripada jumlah diskon!");
		Validator.FocusErrorById("jdiskon");
		return false;
	}
			
	if (jcicilan - jdiskon > sisa)
	{
		alert("Jumlah pembayaran tidak boleh lebih besar daripada sisa tagihan!");
		Validator.FocusErrorById("jcicilan");
		return false;
	}
			
	//if (!confirm("Data sudah benar?"))
	//	return false;
	
	let ktagihan = $("#ktagihan").val().replace("'", "`");
	let kcicilan = $("#kcicilan").val().replace("'", "`");
	let ncicil = $("#ncicil").val();
	let infocicil = "Pembayaran ke-" + ncicil + " " + $("#payment option:selected").text();
	if (jcicilan === sisa)
		infocicil = "Pelunasan " + $("#payment option:selected").text();
	infocicil = infocicil.replace("'", "`");
	let rekkas = $("#rekkas").val();
	let namakas = $("#rekkas option:selected").text();

	let lunas = 0;
	if (tagihan === 0)
		lunas = 2;
	else if (jcicilan === sisa)
		lunas = 1;
		
	totalPayment += jcicilan - jdiskon;
	
	nData += 1;
	let data = "";
	data += "<tr id='row" + nData + "'>\r\n";
	data += "<input type='hidden' id='" + kate + "_" + idpayment + "' value='1'>\r\n";
	data += "<input type='hidden' name='i_kate_" + nData + "' value='" + kate + "'>\r\n";
	data += "<input type='hidden' name='i_idpayment_" + nData + "' value='" + idpayment + "'>\r\n";
	data += "<input type='hidden' name='i_idbesarjtt_" + nData + "' value='" + idbesarjtt + "'>\r\n";
	data += "<input type='hidden' name='i_tagihan_" + nData + "' value='" + tagihan + "'>\r\n";
	data += "<input type='hidden' name='i_bcicilan_" + nData + "' value='" + bcicilan + "'>\r\n";
	data += "<input type='hidden' name='i_ktagihan_" + nData + "' value='" + ktagihan + "'>\r\n";
	data += "<input type='hidden' id='i_jcicilan_" + nData + "' name='i_jcicilan_" + nData + "' value='" + jcicilan + "'>\r\n";
	data += "<input type='hidden' id='i_jdiskon_" + nData + "' name='i_jdiskon_" + nData + "' value='" + jdiskon + "'>\r\n";
	data += "<input type='hidden' name='i_kcicilan_" + nData + "' value='" + kcicilan + "'>\r\n";
	data += "<input type='hidden' name='i_infocicilan_" + nData + "' value='" + infocicil + "'>\r\n";
	data += "<input type='hidden' name='i_rekkas_" + nData + "' value='" + rekkas + "'>\r\n";
	data += "<input type='hidden' name='i_namakas_" + nData + "' value='" + namakas + "'>\r\n";
	data += "<input type='hidden' name='i_lunas_" + nData + "' value='" + lunas + "'>\r\n";
	data += "<td align='left'>" + namakas + "</td>\r\n";
	data += "<td align='left'>" + infocicil + "</td>\r\n";
	data += "<td align='right'>" + numberToRupiah(jcicilan) + "</td>\r\n";
	data += "<td align='right'>" + numberToRupiah(jdiskon) + "</td>\r\n";
	data += "<td align='right'>" + numberToRupiah(jcicilan - jdiskon) + "</td>\r\n";
	data += "<td align='center'>\r\n";
	data += "<img src='../images/ico/hapus.png' title='hapus' style='cursor: pointer' onclick='DeletePayment(" + nData + ")'>\r\n";
	data += "</td>\r\n";
	data += "</tr>\r\n";
	$("#tabPaymentList > tbody:last").append(data);
	$("#spanTotalInfo").text(numberToRupiah(totalPayment));
	
	if ($("#flagrow" + nData).length > 0)
	{
		$("#flagrow" + nData).val(1);
	}
	else
	{
		data = "<input type='hidden' name='flagrow" + nData + "' id='flagrow" + nData + "' value='1'>\r\n";
		$("#mainForm").append(data);
		$("#nflagrow").val(nData + 1);
	}
	
	$("#divPaymentInfo").text("");
};

AddToPaymentListSkr = function()
{
	let kate = $("#kate").val();
	let idpayment = $("#payment").val();
	if ($("#" + kate + "_" + idpayment).length > 0)
	{
		alert("Jenis pembayaran ini sudah ada dalam Daftar Pembayaran!");
		return false;
	}
	
	if (!ValidateRupiahInput("jumlah", "Pembayaran Iuran Sukarela", false))
		return false;
	
	let jumlah = parseInt(GetRupiahValueOf("jumlah"));
	let keterangan = $("#keterangan").val().replace("'", "`");
	let infocicil = "Pembayaran " + $("#payment option:selected").text();
	let rekkas = $("#rekkas").val();
	let namakas = $("#rekkas option:selected").text();
	
	totalPayment += jumlah;

	nData += 1;
	let data = "";
	data += "<tr id='row" + nData + "'>\r\n";
	data += "<input type='hidden' id='" + kate + "_" + idpayment + "' value='1'>\r\n";
	data += "<input type='hidden' name='i_kate_" + nData + "' value='" + kate + "'>\r\n";
	data += "<input type='hidden' name='i_idpayment_" + nData + "' value='" + idpayment + "'>\r\n";
	data += "<input type='hidden' id='i_jumlah_" + nData + "' name='i_jumlah_" + nData + "' value='" + jumlah + "'>\r\n";
	data += "<input type='hidden' id='i_jcicilan_" + nData + "' name='i_jcicilan_" + nData + "' value='" + jumlah + "'>\r\n";
	data += "<input type='hidden' name='i_rekkas_" + nData + "' value='" + rekkas + "'>\r\n";
	data += "<input type='hidden' name='i_namakas_" + nData + "' value='" + namakas + "'>\r\n";
	data += "<input type='hidden' id='i_jdiskon_" + nData + "' name='i_jdiskon_" + nData + "' value='0'>\r\n";
	data += "<input type='hidden' name='i_keterangan_" + nData + "' value='" + keterangan + "'>\r\n";
	data += "<input type='hidden' name='i_infocicilan_" + nData + "' value='" + infocicil + "'>\r\n";
	data += "<td align='left'>" + namakas + "</td>\r\n";
	data += "<td align='left'>" + infocicil + "</td>\r\n";
	data += "<td align='right'>" + numberToRupiah(jumlah) + "</td>\r\n";
	data += "<td align='right'>" + numberToRupiah(0) + "</td>\r\n";
	data += "<td align='right'>" + numberToRupiah(jumlah) + "</td>\r\n";
	data += "<td align='center'>\r\n";
	data += "<img src='../images/ico/hapus.png' title='hapus' style='cursor: pointer;' onclick='DeletePayment(" + nData + ")'>\r\n";
	data += "</td>\r\n";
	data += "</tr>\r\n";
	$("#tabPaymentList > tbody:last").append(data);
	$("#spanTotalInfo").text(numberToRupiah(totalPayment));
	
	if ($("#flagrow" + nData).length > 0)
	{
		$("#flagrow" + nData).val(1);
	}
	else
	{
		data = "<input type='hidden' name='flagrow" + nData + "' id='flagrow" + nData + "' value='1'>\r\n";
		$("#mainForm").append(data);
		$("#nflagrow").val(nData + 1);
	}
	
	$("#divPaymentInfo").text("");
};

AddToPaymentList = function()
{
	let kate = $("#kate").val();
	
	if (kate === "JTT" || kate === "CSWJB")
		return AddToPaymentListWjb();
	else if (kate === "SKR" || kate === "CSSKR")
		return AddToPaymentListSkr();
};

function ValidateSave()
{
	if (nData < 0)
	{
		alert("Anda perlu memasukan minimal satu transaksi!");
		return false;
	}
	
	let nNonZero = 0;
	for (let i = 0; i <= nData; i++)
	{
		let flag = parseInt($("#flagrow" + i).val());
		if (flag !== 0)
            nNonZero += 1;
	}

	if (nNonZero === 0)
	{
		alert("Anda perlu memasukan minimal satu transaksi!");
		return false;
	}
	
	return confirm("Semua data sudah benar?");
}

function showInfoSiswa()
{
    let qsb = new QsBuilder();
    qsb.add("nis", $("#userid").val());

    newWindow('../library/infosiswa.dialog.php?'+qsb.createQs(), 'InformasiSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function showInfoCalonSiswa()
{
    let qsb = new QsBuilder();
    qsb.add("nic", $("#userid").val());

    newWindow('../library/infocalonsiswa.dialog.php?'+qsb.createQs(), 'InformasiCalonSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function showRiwayatPembayaran(kategori, userId, userReplid, idPenerimaan, idTahunBuku)
{
    let qsb = new QsBuilder();
    qsb.add("kategori", kategori);
    qsb.add("userid", userId);
    qsb.add("userreplid", userReplid);
    qsb.add("idpenerimaan", idPenerimaan);
    qsb.add("idtahunbuku", idTahunBuku);

    newWindow('multitrans2.content.riwayat.php?'+qsb.createQs(), 'RiwayatPembayaran','780','580','resizable=1,scrollbars=1,status=0,toolbar=0');
}