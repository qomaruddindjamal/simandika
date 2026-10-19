var ifse_DivList = ['ifse_notes_content', 'ifse_galeri_content', 'ifse_video_content'];
var ifse_MadingPanel = $("#content-pane-j");
var ifse_LastListScrollPos = 0;

/* Tiga kanal punya iframe sendiri. Lembaga terakhir yang sudah dimuat dicatat per
   frame, bukan di satu variabel bersama: dengan satu variabel, mengganti lembaga hanya
   memuat ulang tab yang sedang tampil dan tab lain tetap menampilkan data lembaga
   sebelumnya. */
var ifse_DeptByFrame = { 'ifse_notes_content': '', 'ifse_galeri_content': '', 'ifse_video_content': '' };

ifse_ReloadFrame = function(divname, src, setelahMuat) {
	
	var currdept = $("#ifse_departemen").val();
	if (ifse_DeptByFrame[divname] == currdept)
		return;
	
	ifse_DeptByFrame[divname] = currdept;
	
	var frame = $('#' + divname);
	frame.off('load.ifseDept');
	if (setelahMuat)
	{
		/* Daftar galeri/video dimuat lewat AJAX dari dalam iframe, bukan oleh
		   halaman PHP-nya, jadi pemuatnya harus dipanggil ulang setiap frame
		   berganti lembaga (warisan hanya memanggilnya sekali seumur halaman). */
		frame.on('load.ifseDept', function () {
			frame.off('load.ifseDept');
			setelahMuat(this.contentWindow);
		});
	}
	frame.attr('src', src);
}

ifse_SetVisible = function(divname) {
	
	for(var i = 0; i < ifse_DivList.length; i++)
	{
		if (ifse_DivList[i] == divname)
			$("#" + ifse_DivList[i]).css('visibility', 'visible');
		else
			$("#" + ifse_DivList[i]).css('visibility', 'hidden');
	}
}

ifse_ShowNotes = function() {
	
	ifse_SetVisible('ifse_notes_content');
	ifse_ReloadFrame('ifse_notes_content', 'infosekolah/notes.php');
	
}

ifse_ShowGaleri = function(src) {
	
	ifse_SetVisible('ifse_galeri_content');
	ifse_ReloadFrame('ifse_galeri_content', 'infosekolah/galeri.php', function (win) {
		win.gallst_GetGalleryList();
	});
	
}

ifse_ShowVideo = function() {
	
	ifse_SetVisible('ifse_video_content');
	ifse_ReloadFrame('ifse_video_content', 'infosekolah/video.php', function (win) {
		win.vidlst_GetVideoList();
	});
	
}

ifse_ResizeIFrame = function() {
	
	var docHeight = $(document).height();
	var iframeHeight = docHeight - 220;
	
	$('#ifse_notes_content').css('height', iframeHeight);
	$('#ifse_galeri_content').css('height', iframeHeight);
	$('#ifse_video_content').css('height', iframeHeight);
}

ifse_InitAllContent = function() {
	
	var currdept = $("#ifse_departemen").val();
	ifse_DeptByFrame['ifse_notes_content'] = currdept;
	ifse_DeptByFrame['ifse_galeri_content'] = currdept;
	ifse_DeptByFrame['ifse_video_content'] = currdept;
	
	$('#ifse_notes_content').attr('src', 'infosekolah/notes.php');
	$('#ifse_galeri_content').attr('src', 'infosekolah/galeri.php');
	$('#ifse_video_content').attr('src', 'infosekolah/video.php');
	
}

ifse_InitInfoSekolah = function() {
	
	ifse_ResizeIFrame();
	ifse_InitAllContent();
	
}

// ON WINDOW RESIZED
$(window).resize(function() {
	
    ifse_ResizeIFrame();
	
});
