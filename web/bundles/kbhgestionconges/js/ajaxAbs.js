$("document").ready( function() {

    //declenchement de la fonction au changement du nombre de jours
    $("#btn-motif").click( function() {
     if ($("#motifAbs").val.length !== null )  {
         
        $.ajax({
            type : "GET",
            url : "http://www.grhappdemo.ivolife.com/web/app_dev.php/dashboard-salarie/absence/duree/"+$("select[id='btn-motif'] option:selected").val(),
            beforeSend : function() {
                        if ( $(".loading").length === 0 ) {
                        $("#kbh_gestioncongesbundle_demande_nbJoursOuvrables").parent().append('<i class="loading" style="margin-right:-40px;margin-top:-31px;"></i>');
                        console.log("chargement !!!");
                     }
            },
            success : function(data) {
                $("#kbh_gestioncongesbundle_demande_nbJoursOuvrables").val(data.dureeAbsence);
                $("#kbh_gestioncongesbundle_demande_motif").val($("select[id='btn-motif'] option:selected").val());
                $("#nbJours").val(data.dureeAbsence);
                $(".loading").remove();
                console.log("duree du motif :"+$("select[id='btn-motif'] option:selected").val()+" recuperee!!");
                console.log("motif recupéré :"+$("#kbh_gestioncongesbundle_demande_motif").val()+" recuperee!!");
            },
            error : function() {
                $("#kbh_gestioncongesbundle_demande_nbJoursOuvrables").val("");
                $("#nbJours").val("");
                $("#kbh_gestioncongesbundle_demande_motif").val("");
                $(".loading").remove();
            }
            
        });

       }
        
    });


});