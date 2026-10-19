var mad_ActiveTab = 'notes';
var mad_VideoFirstAccess = true;
var mad_GalleryFirstAccess = true;

mad_MenuNotesClicked = function() {
	
	if (mad_ActiveTab == 'notes')
		return;
	
	$('#mad_menu_notes_image').attr("src", "images/tab-aktif.png");
	$('#mad_menu_galeri_image').attr("src", "images/tab-pasif.png");
	$('#mad_menu_video_image').attr("src", "images/tab-pasif.png");
	
	mad_ShowNotes();
	mad_ActiveTab = 'notes';
	
}

mad_MenuGaleriClicked = function() {
	
	if (mad_ActiveTab == 'galeri')
		return;
	
	$('#mad_menu_notes_image').attr("src", "images/tab-pasif.png");
	$('#mad_menu_galeri_image').attr("src", "images/tab-aktif.png");
	$('#mad_menu_video_image').attr("src", "images/tab-pasif.png");

	mad_ShowGaleri();
	mad_ActiveTab = 'galeri';
	
	if (mad_GalleryFirstAccess)
	{
		top.frames['mad_galeri_content'].gallst_GetGalleryList();
		mad_GalleryFirstAccess = false;
	}

}

mad_MenuVideoClicked = function() {
	
	if (mad_ActiveTab == 'video')
		return;
	
	$('#mad_menu_notes_image').attr("src", "images/tab-pasif.png");
	$('#mad_menu_galeri_image').attr("src", "images/tab-pasif.png");
	$('#mad_menu_video_image').attr("src", "images/tab-aktif.png");
	
	mad_ShowVideo();
	mad_ActiveTab = 'video';
	
	if (mad_VideoFirstAccess)
	{
		top.frames['mad_video_content'].vidlst_GetVideoList();
		mad_VideoFirstAccess = false;
	}
	
}

mad_DepartemenChanged = function() {
	
	/* Cabang di bawah ini dulu ketiganya =="notes", jadi galeri & video tidak pernah
	   dimuat ulang. Show* mencatat lembaga per frame, sehingga tab yang sedang tidak
	   tampil ikut tersegar saat diklik setelah lembaga diganti. */
	if (mad_ActiveTab == "galeri")
		mad_ShowGaleri();
	else if (mad_ActiveTab == "video")
		mad_ShowVideo();
	else
		mad_ShowNotes();
		
}