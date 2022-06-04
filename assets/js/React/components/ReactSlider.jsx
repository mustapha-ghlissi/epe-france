import React, {Component} from 'react';
import Slider from "react-slick";
import _ from "lodash";
import Note from "./Note";

class ReactSlider extends Component {


    getFormByCategory = () => {
        const {categoryId, data} = this.props;

        switch (categoryId) {
            case 1:
                return (
                    data.municipalAdvisors.map((municipalAdvisor, index) => (
                        <form
                            key={index}
                            action="/liste-des-conseillers-municipaux/evaluation"
                            method="post">
                            <input type="hidden" name="memberId" value={municipalAdvisor.id}/>
                            <input type="hidden" name="categoryId" value="1"/>
                            <div className="p-1 form-vote">
                                <div className='card'>
                                    <div className="card-body text-center">
                                        <div
                                            className="d-flex flex-row justify-content-center align-items-center mb-3">
                                            <img className="rounded-circle elected-avatar"
                                                 src="build/images/member-logo.bcd1c003.jpg"
                                                 alt=""/>
                                        </div>
                                        <h6 className='font-weight-bold'>
                                            {_.capitalize(municipalAdvisor.firstName) + ' ' + _.toUpper(municipalAdvisor.lastName)}
                                        </h6>

                                        <div
                                            className="d-flex flex-row flex-wrap align-items-center justify-content-center mb-3">
                                            <Note note={municipalAdvisor.note ?? null}/>
                                            <span className='badge badge-warning badge-pill'>
                                            {municipalAdvisor.countNotes ?? 0} votes</span>
                                        </div>

                                        <span>
                                            {_.capitalize(municipalAdvisor.communeLabel)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    ))
                )
                break;
            case 2:
                return (
                    data.communityAdvisors.map((communityAdvisor, index) => (
                        <form
                            key={index}
                            action="/liste-des-conseillers-communautaires/evaluation"
                            method="post">
                            <input type="hidden" name="memberId" value={communityAdvisor.id}/>
                            <input type="hidden" name="categoryId" value="2"/>
                            <div className="p-1 form-vote">
                                <div className='card'>
                                    <div className="card-body text-center">
                                        <div
                                            className="d-flex flex-row justify-content-center align-items-center mb-3">
                                            <img className="rounded-circle elected-avatar"
                                                 src="build/images/member-logo.bcd1c003.jpg"
                                                 alt=""/>
                                        </div>
                                        <h6 className='font-weight-bold'>
                                            {_.capitalize(communityAdvisor.firstName) + ' ' + _.toUpper(communityAdvisor.lastName)}
                                        </h6>
                                        <div
                                            className="d-flex flex-row flex-wrap align-items-center justify-content-center mb-3">
                                            <Note note={communityAdvisor.note ?? null}/>
                                            <span className='badge badge-warning badge-pill'>
                                            {communityAdvisor.countNotes ?? 0} votes</span>
                                        </div>
                                        <span>
                                            {_.capitalize(communityAdvisor.communeLabel)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    ))
                )
                break;
            case 3:
                return (
                    data.departmentalAdvisors.map((departmentalAdvisor, index) => (
                        <form
                            key={index}
                            action="/liste-des-conseillers-departementaux/evaluation"
                            method="post">
                            <input type="hidden" name="memberId" value={departmentalAdvisor.id}/>
                            <input type="hidden" name="categoryId" value="3"/>
                            <div className="p-1 form-vote">
                                <div className='card'>
                                    <div className="card-body text-center">
                                        <div
                                            className="d-flex flex-row justify-content-center align-items-center mb-3">
                                            <img className="rounded-circle elected-avatar"
                                                 src="build/images/member-logo.bcd1c003.jpg"
                                                 alt=""/>
                                        </div>
                                        <h6 className='font-weight-bold'>
                                            {_.capitalize(departmentalAdvisor.firstName) + ' ' + _.toUpper(departmentalAdvisor.lastName)}
                                        </h6>
                                        <div
                                            className="d-flex flex-row flex-wrap align-items-center justify-content-center mb-3">
                                            <Note note={departmentalAdvisor.note ?? null}/>
                                            <span className='badge badge-warning badge-pill'>
                                            {departmentalAdvisor.countNotes ?? 0} votes</span>
                                        </div>
                                        <span>
                                            {_.capitalize(departmentalAdvisor.departmentLabel)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    ))
                );
                break;
            case 4:
                return (
                    data.regionalAdvisors.map((regionalAdvisor, index) => (
                        <form
                            key={index}
                            action="/liste-des-conseillers-regionaux/evaluation"
                            method="post">
                            <input type="hidden" name="memberId" value={regionalAdvisor.id}/>
                            <input type="hidden" name="categoryId" value="4"/>
                            <div className="p-1 form-vote">
                                <div className='card'>
                                    <div className="card-body text-center">
                                        <div
                                            className="d-flex flex-row justify-content-center align-items-center mb-3">
                                            <img className="rounded-circle elected-avatar"
                                                 src="build/images/member-logo.bcd1c003.jpg"
                                                 alt=""/>
                                        </div>
                                        <h6 className='font-weight-bold'>
                                            {_.capitalize(regionalAdvisor.firstName) + ' ' + _.toUpper(regionalAdvisor.lastName)}
                                        </h6>
                                        <div
                                            className="d-flex flex-row flex-wrap align-items-center justify-content-center mb-3">
                                            <Note note={regionalAdvisor.note ?? null}/>
                                            <span className='badge badge-warning badge-pill'>
                                            {regionalAdvisor.countNotes ?? 0} votes</span>
                                        </div>
                                        <span>
                                            {_.capitalize(regionalAdvisor.areaLabel)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    ))
                );
                break;
            case 5:
                return (
                    data.corsicanAdvisors.map((corsicanAdvisor, index) => (
                        <form
                            key={index}
                            action="/liste-des-conseillers-corse/evaluation"
                            method="post">
                            <input type="hidden" name="memberId" value={corsicanAdvisor.id}/>
                            <input type="hidden" name="categoryId" value="5"/>
                            <div className="p-1 form-vote">
                                <div className='card'>
                                    <div className="card-body text-center">
                                        <div
                                            className="d-flex flex-row justify-content-center align-items-center mb-3">
                                            <img className="rounded-circle elected-avatar"
                                                 src="build/images/member-logo.bcd1c003.jpg"
                                                 alt=""/>
                                        </div>
                                        <h6 className='font-weight-bold'>
                                            {_.capitalize(corsicanAdvisor.firstName) + ' ' + _.toUpper(corsicanAdvisor.lastName)}
                                        </h6>
                                        <div
                                            className="d-flex flex-row flex-wrap align-items-center justify-content-center mb-3">
                                            <Note note={corsicanAdvisor.note ?? null}/>
                                            <span className='badge badge-warning badge-pill'>
                                            {corsicanAdvisor.countNotes ?? 0} votes</span>
                                        </div>
                                        <span>
                                            {_.capitalize(corsicanAdvisor.departmentLabel)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    ))
                );
                break;
            case 6:
                return (
                    data.euroDeputies.map((euroDeputy, index) => (
                        <form
                            key={index}
                            action="/liste-des-deputes-europeens/evaluation"
                            method="post">
                            <input type="hidden" name="memberId" value={euroDeputy.id}/>
                            <input type="hidden" name="categoryId" value="6"/>
                            <div className="p-1 form-vote">
                                <div className='card'>
                                    <div className="card-body text-center">
                                        <div
                                            className="d-flex flex-row justify-content-center align-items-center mb-3">
                                            <img className="rounded-circle elected-avatar"
                                                 src="build/images/member-logo.bcd1c003.jpg"
                                                 alt=""/>
                                        </div>
                                        <h6 className='font-weight-bold'>
                                            {_.capitalize(euroDeputy.firstName) + ' ' + _.toUpper(euroDeputy.lastName)}
                                        </h6>
                                        <div
                                            className="d-flex flex-row flex-wrap align-items-center justify-content-center mb-3">
                                            <Note note={euroDeputy.note ?? null}/>
                                            <span className='badge badge-warning badge-pill'>
                                            {euroDeputy.countNotes ?? 0} votes</span>
                                        </div>
                                        <span>
                                            {_.capitalize(euroDeputy.professionLabel)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    ))
                );
                break;
            case 7:
                return (
                    data.senators.map((senator, index) => (
                        <form
                            key={index}
                            action="/liste-des-senateurs/evaluation"
                            method="post">
                            <input type="hidden" name="memberId" value={senator.id}/>
                            <input type="hidden" name="categoryId" value="7"/>
                            <div className="p-1 form-vote">
                                <div className='card'>
                                    <div className="card-body text-center">
                                        <div
                                            className="d-flex flex-row justify-content-center align-items-center mb-3">
                                            <img className="rounded-circle elected-avatar"
                                                 src="build/images/member-logo.bcd1c003.jpg"
                                                 alt=""/>
                                        </div>
                                        <h6 className='font-weight-bold'>
                                            {_.capitalize(senator.firstName) + ' ' + _.toUpper(senator.lastName)}
                                        </h6>
                                        <div
                                            className="d-flex flex-row flex-wrap align-items-center justify-content-center mb-3">
                                            <Note note={senator.note ?? null}/>
                                            <span className='badge badge-warning badge-pill'>
                                            {senator.countNotes ?? 0} votes</span>
                                        </div>
                                        <span>
                                            {_.capitalize(senator.departmentLabel)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    ))
                );
                break;
            case 8:
                return (
                    data.deputies.map((deputy, index) => (
                        <form
                            key={index}
                            action="/liste-des-deputes/evaluation"
                            method="post">
                            <input type="hidden" name="memberId" value={deputy.id}/>
                            <input type="hidden" name="categoryId" value="8"/>
                            <div className="p-1 form-vote">
                                <div className='card'>
                                    <div className="card-body text-center">
                                        <div
                                            className="d-flex flex-row justify-content-center align-items-center mb-3">
                                            <img className="rounded-circle elected-avatar"
                                                 src="build/images/member-logo.bcd1c003.jpg"
                                                 alt=""/>
                                        </div>
                                        <h6 className='font-weight-bold'>
                                            {_.capitalize(deputy.firstName) + ' ' + _.toUpper(deputy.lastName)}
                                        </h6>
                                        <div
                                            className="d-flex flex-row flex-wrap align-items-center justify-content-center mb-3">
                                            <Note note={deputy.note ?? null}/>
                                            <span className='badge badge-warning badge-pill'>
                                            {deputy.countNotes ?? 0} votes</span>
                                        </div>
                                        <span>
                                            {_.capitalize(deputy.departmentLabel)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    ))
                )
                break;
            case 9:
                return (
                    data.mayors.map((mayor, index) => (
                        <form
                            key={index}
                            action="/liste-des-maires/evaluation"
                            method="post">
                            <input type="hidden" name="memberId" value={mayor.id}/>
                            <input type="hidden" name="categoryId" value="9"/>
                            <div className="p-1 form-vote">
                                <div className='card'>
                                    <div className="card-body text-center">
                                        <div
                                            className="d-flex flex-row justify-content-center align-items-center mb-3">
                                            <img className="rounded-circle elected-avatar"
                                                 src="build/images/member-logo.bcd1c003.jpg"
                                                 alt=""/>
                                        </div>
                                        <h6 className='font-weight-bold'>
                                            {_.capitalize(mayor.firstName) + ' ' + _.toUpper(mayor.lastName)}
                                        </h6>
                                        <div
                                            className="d-flex flex-row flex-wrap align-items-center justify-content-center mb-3">
                                            <Note note={mayor.note ?? null}/>
                                            <span className='badge badge-warning badge-pill'>
                                            {mayor.countNotes ?? 0} votes</span>
                                        </div>
                                        <span>
                                            {_.capitalize(mayor.communeLabel)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    ))
                )
                break;
            case 10:
                return (
                    data.departmentalPresidents.map((departmentalPresident, index) => (
                        <form
                            key={index}
                            action="/liste-des-presidents-des-departements/evaluation"
                            method="post">
                            <input type="hidden" name="memberId" value={departmentalPresident.id}/>
                            <input type="hidden" name="categoryId" value="10"/>
                            <div className="p-1 form-vote">
                                <div className='card'>
                                    <div className="card-body text-center">
                                        <div
                                            className="d-flex flex-row justify-content-center align-items-center mb-3">
                                            <img className="rounded-circle elected-avatar"
                                                 src="build/images/member-logo.bcd1c003.jpg"
                                                 alt=""/>
                                        </div>
                                        <h6 className='font-weight-bold'>
                                            {_.capitalize(departmentalPresident.firstName) + ' ' + _.toUpper(departmentalPresident.lastName)}
                                        </h6>
                                        <div
                                            className="d-flex flex-row flex-wrap align-items-center justify-content-center mb-3">
                                            <Note note={departmentalPresident.note ?? null}/>
                                            <span className='badge badge-warning badge-pill'>
                                            {departmentalPresident.countNotes ?? 0} votes</span>
                                        </div>
                                        <span>
                                            {_.capitalize(departmentalPresident.departmentLabel)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    ))
                )
                break;
            default:
                return (
                    data.regionalPresidents.map((regionalPresident, index) => (
                        <form
                            key={index}
                            action="/liste-des-presidents-des-regions/evaluation"
                            method="post">
                            <input type="hidden" name="memberId" value={regionalPresident.id}/>
                            <input type="hidden" name="categoryId" value="11"/>
                            <div className="p-1 form-vote">
                                <div className='card'>
                                    <div className="card-body text-center">
                                        <div
                                            className="d-flex flex-row justify-content-center align-items-center mb-3">
                                            <img className="rounded-circle elected-avatar"
                                                 src="build/images/member-logo.bcd1c003.jpg"
                                                 alt=""/>
                                        </div>
                                        <h6 className='font-weight-bold'>
                                            {_.capitalize(regionalPresident.firstName) + ' ' + _.toUpper(regionalPresident.lastName)}
                                        </h6>
                                        <div
                                            className="d-flex flex-row flex-wrap align-items-center justify-content-center mb-3">
                                            <Note note={regionalPresident.note ?? null}/>
                                            <span className='badge badge-warning badge-pill'>
                                            {regionalPresident.countNotes ?? 0} votes</span>
                                        </div>
                                        <span>
                                            {_.capitalize(regionalPresident.areaLabel)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    ))
                )
                break;
        }
    }


    render() {
        const {data, categoryId} = this.props;

        const settings = {
            dots: true,
            infinite: false,
            speed: 500,
            slidesToShow: 4,
            slidesToScroll: 4,
            initialSlide: 0,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        initialSlide: 2
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        };

        return (
            <Slider {...settings}>
                {this.getFormByCategory()}
            </Slider>
        );
    }
}

export default ReactSlider;