$("document").ready( function() {

    //declenchement de la fonction au changement du nombre de jours
    $("#nbJours").keyup( function() {
     if ($("#nbJours").val.length !== 0 )  {
        $.ajax({
            type : "GET",
            url : "http://www.grhappdemo.ivolife.com/web/app_dev.php/dashboard-salarie/date/"+$("#nbJours").val()+"/"+$("#kbh_gestioncongesbundle_demande_dateDebut").val(),
            beforeSend : function() {
                        if ( $(".loading").length === 0 ) {
                        $("#kbh_gestioncongesbundle_demande_dateFin").parent().append('<i class="loading" style="margin-right:15px"></i>');
                        console.log("chargement !!!");
                     }
            },
            success : function(data) {
                $("#kbh_gestioncongesbundle_demande_dateFin").val(data.DateFin);
                $("#kbh_gestioncongesbundle_demande_nbJoursOuvrables").val($("#nbJours").val());
                $("#dateRetour").val(data.RetourConge);                
                $(".loading").remove();
                console.log("date : "+data.RetourConge+" okey!!!");
            },
            error : function() {
                $("#kbh_gestioncongesbundle_demande_dateFin").val("");
                $("#kbh_gestioncongesbundle_demande_nbJoursOuvrables").val("");
                $("#dateRetour").val("");    
            $(".loading").remove();
            }
            
        });

       }
        
    });
    
    //declenchement de la fonction au click sur le datepicker
     $("#kbh_gestioncongesbundle_demande_dateDebut").click( function() {
     if ($("#kbh_gestioncongesbundle_demande_dateDebut").val.length !== 0 )  {
        $.ajax({
            type : "GET",
            url : "http://www.grhappdemo.ivolife.com/web/app_dev.php/dashboard-salarie/date/"+$("#nbJours").val()+"/"+$("#kbh_gestioncongesbundle_demande_dateDebut").val(),
            beforeSend : function() {
                        if ( $(".loading").length === 0 ) {
                        $("#kbh_gestioncongesbundle_demande_dateFin").parent().append('<i class="loading" style="margin-right:15px"></i>');
                        console.log("chargement !!!");
                     }
            },
            success : function(data) {
                $("#kbh_gestioncongesbundle_demande_dateFin").val(data.DateFin);
                $("#kbh_gestioncongesbundle_demande_nbJoursOuvrables").val($("#nbJours").val());
                $("#dateRetour").val(data.RetourConge);            
                $(".loading").remove();
                console.log("date : "+data.RetourConge+" okey!!!");
            },
            error : function() {
                $("#kbh_gestioncongesbundle_demande_dateFin").val("");
                $("#kbh_gestioncongesbundle_demande_nbJoursOuvrables").val("");
                $("#dateRetour").val("");          
            $(".loading").remove();
            }
            
        });

       }
        
    });
    
 

});