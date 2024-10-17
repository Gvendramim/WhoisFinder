jQuery(document).ready(function($) {

    // Função para buscar detalhes do IP
    function fetchIPDetails() {
        $.ajax({
            url: wf_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'wf_fetch_ip_details',
                nonce: wf_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    const ipData = response.data;
                    $('#wf-user-ip').text(ipData.ip);
                    $('#wf-ip-info').html(`
                        <p><span>País:</span> ${ipData.country}</p>
                        <p><span>Cidade:</span> ${ipData.city}</p>
                        <p><span>Provedor:</span> ${ipData.isp}</p>
                        <p><span>Latitude:</span> ${ipData.latitude}</p>
                        <p><span>Longitude:</span> ${ipData.longitude}</p>
                    `);
                } else {
                    $('#wf-ip-info').text('Não foi possível carregar as informações do IP.');
                }
            }
        });
    }

    // Função para buscar informações Whois
    $('#wf-buscar-btn').on('click', function() {
        const domain = $('#wf-domain-input').val();
        if (domain === '') {
            alert('Por favor, insira um domínio.');
            return;
        }

        $.ajax({
            url: wf_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'wf_fetch_whois_info',
                nonce: wf_ajax_object.nonce,
                domain: domain
            },
            success: function(response) {
                if (response.success) {
                    const whoisData = response.data;
                    $('#wf-whois-info').html(`
                        <p><span>Domínio:</span> ${whoisData.domainName}</p>
                        <p><span>Data de Criação:</span> ${whoisData.createdDate}</p>
                        <p><span>Última Atualização:</span> ${whoisData.updatedDate}</p>
                        <p><span>Servidor Whois:</span> ${whoisData.whoisServer}</p>
                        <p><span>Status:</span> ${whoisData.status}</p>
                    `);
                } else {
                    $('#wf-whois-info').text('Não foi possível obter informações Whois.');
                }
            }
        });
    });
    
    fetchIPDetails();
});
