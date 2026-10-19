var mad_DivList = ['mad_notes_content', 'mad_galeri_content', 'mad_video_content'];
var mad_MadingPanel = $("#content-pane-j");
var mad_LastListScrollPos = 0;

/* Tiga kanal punya iframe sendiri. Lembaga terakhir yang sudah dimuat dicatat per
   frame, bukan di satu variabel bersama: dengan satu variabel, mengganti lembaga hanya
   memuat ulang tab yang sedang tampil dan tab lain tetap menampilkan data lembaga
   sebelumnya. */
var mad_DeptByFrame = { 'mad_notes_content': '', 'mad_galeri_content': '', 'mad_video_content': '' };

mad_ReloadFrame = function(divname, src, setelahMuat) {
	
	var currdept = $("#mad_departemen").val();
	if (mad_DeptByFrame[divname] == currdept)
		return;
	
	mad_DeptByFrame[divname] = currdept;
	
	var frame = $('#' + divname);
	frame.off('load.madDept');
	if (setelahMuat)
	{
		/* Daftar galeri/video dimuat lewat AJAX dari dalam iframe, bukan oleh
		   halaman PHP-nya, jadi pemuatnya harus dipanggil ulang setiap frame
		   berganti lembaga (warisan hanya memanggilnya sekali seumur halaman). */
		frame.on('load.madDept', function () {
			frame.off('load.madDept');
			setelahMuat(this.contentWindow);
		});
	}
	frame.attr('src', src);
}

mad_SetVisible = function(divname) {
	
	for(var i = 0; i < mad_DivList.length; i++)
	{
		if (mad_DivList[i] == divname)
			$("#" + mad_DivList[i]).css('visibility', 'visible');
		else
			$("#" + mad_DivList[i]).css('visibility', 'hidden');
	}
}

mad_ShowNotes = function() {
	
	mad_SetVisible('mad_notes_content');
	mad_ReloadFrame('mad_notes_content', 'mading/notes.php');
	
}

mad_ShowGaleri = function(src) {
	
	mad_SetVisible('mad_galeri_content');
	mad_ReloadFrame('mad_galeri_content', 'mading/galeri.php', function (win) {
		win.gallst_GetGalleryList();
	});
	
}

mad_ShowVideo = function() {
	
	mad_SetVisible('mad_video_content');
	mad_ReloadFrame('mad_video_content', 'mading/video.php', function (win) {
		win.vidlst_GetVideoList();
	});
	
}

mad_ResizeIFrame = function() {
	
	var docHeight = $(document).height();
	var iframeHeight = docHeight - 220;
	
	$('#mad_notes_content').css('height', iframeHeight);
	$('#mad_galeri_content').css('height', iframeHeight);
	$('#mad_video_content').css('height', iframeHeight);
}

mad_InitAllContent = function() {
	
	var currdept = $("#mad_departemen").val();
	mad_DeptByFrame['mad_notes_content'] = currdept;
	mad_DeptByFrame['mad_galeri_content'] = currdept;
	mad_DeptByFrame['mad_video_content'] = currdept;
	
	$('#mad_notes_content').attr('src', 'mading/notes.php');
	$('#mad_galeri_content').attr('src', 'mading/galeri.php');
	$('#mad_video_content').attr('src', 'mading/video.php');
	
}

mad_InitMading = function() {
	
	mad_ResizeIFrame();
	mad_InitAllContent();
	
}

// ON WINDOW RESIZED
$(window).resize(function() {
	
    mad_ResizeIFrame();
	
});
