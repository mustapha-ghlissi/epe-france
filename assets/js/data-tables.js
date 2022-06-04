// DatatTable configuration
import $ from "jquery";
import moment from 'moment';

$('.dataTable').DataTable({
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
});

$('.dataTable-municipals').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "firstName" },
        { "data": "lastName" },
        { "data": "gender" },
        { "data": "birthDate", "render": function ( data, type, row, meta ) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/municipal/advisor/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                        <li class="p-1">
                            <a href="/manager/municipal/advisor/${data.id}/edit"
                               class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});

$('.dataTable-departmentals').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "firstName" },
        { "data": "lastName" },
        { "data": "gender" },
        { "data": "birthDate", "render": function ( data, type, row, meta ) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/departmental/advisor/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                        <li class="p-1">
                            <a href="/manager/departmental/advisor/${data.id}/edit"
                               class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});

$('.dataTable-regionals').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "firstName" },
        { "data": "lastName" },
        { "data": "gender" },
        { "data": "birthDate", "render": function ( data, type, row, meta ) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/regional/advisor/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                        <li class="p-1">
                            <a href="/manager/regional/advisor/${data.id}/edit"
                               class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});

$('.dataTable-communities').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "firstName" },
        { "data": "lastName" },
        { "data": "gender" },
        { "data": "birthDate", "render": function ( data, type, row, meta ) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/community/advisor/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                        <li class="p-1">
                            <a href="/manager/community/advisor/${data.id}/edit"
                               class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});

$('.dataTable-mayors').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "firstName" },
        { "data": "lastName" },
        { "data": "gender" },
        { "data": "birthDate", "render": function ( data, type, row, meta ) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/mayor/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                        <li class="p-1">
                            <a href="/manager/mayor/${data.id}/edit"
                               class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});

$('.dataTable-corsicans').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "firstName" },
        { "data": "lastName" },
        { "data": "gender" },
        { "data": "birthDate", "render": function ( data, type, row, meta ) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/corsican/advisor/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                        <li class="p-1">
                            <a href="/manager/corsican/advisor/${data.id}/edit"
                               class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});

$('.dataTable-senators').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "firstName" },
        { "data": "lastName" },
        { "data": "gender" },
        { "data": "birthDate", "render": function ( data, type, row, meta ) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/senator/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                        <li class="p-1">
                            <a href="/manager/senator/${data.id}/edit"
                               class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});

$('.dataTable-deputies').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "firstName" },
        { "data": "lastName" },
        { "data": "gender" },
        { "data": "birthDate", "render": function ( data, type, row, meta ) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/deputy/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                        <li class="p-1">
                            <a href="/manager/deputy/${data.id}/edit"
                               class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});

$('.dataTable-euro-deputies').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "firstName" },
        { "data": "lastName" },
        { "data": "gender" },
        { "data": "birthDate", "render": function ( data, type, row, meta ) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/euro/deputy/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                        <li class="p-1">
                            <a href="/manager/euro/deputy/${data.id}/edit"
                               class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});



$('.dataTable-taxes').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "year" },
        { "data": "codeInsee" },
        { "data": "communeLabel" },
        { "data": "departmentLabel" },
        { "data": "areaLabel" },
        { "data": "totalAmount", "render": function ( data, type, row, meta ) {
                return data + ' €';
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/tax/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                        <li class="p-1">
                            <a href="/manager/tax/${data.id}/edit"
                               class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});



$('.dataTable-accountings').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "year" },
        { "data": "codeInsee" },
        { "data": "communeLabel" },
        { "data": "departmentLabel" },
        { "data": "areaLabel" },
        { "data": "population", "render": function ( data, type, row, meta ) {
                return new Intl.NumberFormat('fr-FR', { style: 'decimal' }).format(data);
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/accounting/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                        <li class="p-1">
                            <a href="/manager/accounting/${data.id}/edit"
                               class="btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});





$('.dataTable-deputy-notes').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "ipAddress" },
        { "data": "evaluationDate", render: (data, type, row, meta) => {
                return moment(data).format('DD/MM/YYYY HH:mm')
            }
        },
        { "data": "deputy.firstName" },
        { "data": "deputy.lastName" },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/note/deputy/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});


$('.dataTable-euro-deputy-notes').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "ipAddress" },
        { "data": "evaluationDate", render: (data, type, row, meta) => {
                return moment(data).format('DD/MM/YYYY HH:mm')
            }
        },
        { "data": "euroDeputy.firstName" },
        { "data": "euroDeputy.lastName" },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/note/euro/deputy/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});


$('.dataTable-mpdpr-notes').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "ipAddress" },
        { "data": "evaluationDate", render: (data, type, row, meta) => {
                return moment(data).format('DD/MM/YYYY HH:mm')
            }
        },
        { "data": null, render: (data, type, row, meta) => {
                let { mayor, departmentalPresident, regionalPresident } = data;

                if(mayor) {
                    return mayor.firstName;
                }
                else if(departmentalPresident) {
                    return departmentalPresident.firstName;
                }
                else {
                    return regionalPresident.firstName;
                }
            }
        },
        { "data": null, render: (data, type, row, meta) => {
                let { mayor, departmentalPresident, regionalPresident } = data;

                if(mayor) {
                    return mayor.lastName;
                }
                else if(departmentalPresident) {
                    return departmentalPresident.lastName;
                }
                else {
                    return regionalPresident.lastName;
                }
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/note/m/p/d/p/r/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});


$('.dataTable-other-notes').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": 'id'},
        { "data": "ipAddress" },
        { "data": "evaluationDate", render: (data, type, row, meta) => {
                return moment(data).format('DD/MM/YYYY HH:mm')
            }
        },
        { "data": null, render: (data, type, row, meta) => {
                let { communityAdvisor, departmentalAdvisor, regionalAdvisor, senator, corsicanAdvisor, municipalAdvisor } = data;

                if(communityAdvisor) {
                    return communityAdvisor.firstName;
                }
                else if(departmentalAdvisor) {
                    return departmentalAdvisor.firstName;
                }
                else if(regionalAdvisor) {
                    return regionalAdvisor.firstName;
                }
                else if(senator) {
                    return senator.firstName;
                }
                else if(corsicanAdvisor) {
                    return corsicanAdvisor.firstName;
                }
                else {
                    return municipalAdvisor.firstName;
                }
            }
        },
        { "data": null, render: (data, type, row, meta) => {
                let { communityAdvisor, departmentalAdvisor, regionalAdvisor, senator, corsicanAdvisor, municipalAdvisor } = data;

                if(communityAdvisor) {
                    return communityAdvisor.lastName;
                }
                else if(departmentalAdvisor) {
                    return departmentalAdvisor.lastName;
                }
                else if(regionalAdvisor) {
                    return regionalAdvisor.lastName;
                }
                else if(senator) {
                    return senator.lastName;
                }
                else if(corsicanAdvisor) {
                    return corsicanAdvisor.lastName;
                }
                else {
                    return municipalAdvisor.lastName;
                }
            }
        },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/note/other/${data.id}"
                               class="btn btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                    </ul>`
            }
        }
    ]
});




$('.dataTable-board-minutes').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    },
    ajax: `${window.location.href}`,
    columns: [
        { "data": null, render: (data, type, row, meta) => {

            let { commune, department, area } = data;

                if(commune) {
                    return commune.name;
                }
                else if(department) {
                    return department.name;
                }

                return area.name;
            }
        },
        { "data": "title" },
        { "data": "month" },
        { "data": "year" },
        {
            "orderable": false,
            "searchable": false,
            "data": null,
            "render": function( data, type, row, meta ) {
                return `
                    <ul class="list-unstyled d-inline-flex align-items-center">
                        <li class="p-1">
                            <a href="/manager/board/minute/${data.id}"
                               class="btn btn-secondary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                    </ul>
                `
            }
        }
    ]
});
