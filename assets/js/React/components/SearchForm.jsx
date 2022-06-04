import React, {Component} from 'react';
import Slider from "react-slick";
import Select from 'react-select';
import axios from "axios";
import TypeIt from "typeit-react";
import _ from "lodash";


const customStyles = {
    control: (props) => ({
        // none of react-select's styles are passed to <Control />
        ...props,
        height: 50,
        minWidth: 300,
        border: 'none',
        borderRadius: '25px 0 0 25px'
    }),
}

const customMobileStyles = {
    control: (props) => ({
        // none of react-select's styles are passed to <Control />
        ...props,
        height: 50,
        width: '100%',
        border: 'none',
        borderRadius: '25px'
    }),
}

const settings = {
    arrows: false,
    dots: true,
    autoplay: true,
    autoplaySpeed: 5000,
    fade: true,
    speed: 600,
    cssEase: 'linear',
    infinite: true
}
const options = [
    {value: 1, label: 'Conseiller municipal'},
    {value: 2, label: 'Conseiller communautaire'},
    {value: 3, label: 'Conseiller départemental'},
    {value: 4, label: 'Conseiller régional'},
    {value: 5, label: 'Conseiller corse'},
    {value: 6, label: 'Député européen'},
    {value: 7, label: 'Sénateur'},
    {value: 8, label: 'Député'},
    {value: 9, label: 'Maire'},
    {value: 10, label: 'Président de département'},
    {value: 11, label: 'Président de région'},
]


class SearchForm extends Component {

    constructor(props) {
        super(props);

        this.state = {
            categoryId: null,
            isDisabled: true,
            members: null,
            config: null,
            isOpenList: false,
            isLoading: false
        }

        this.searchForm = React.createRef();
    }

    componentDidMount() {
        document.addEventListener('mousedown', this.closeMemberList);
    }

    closeMemberList = (event) => {
        if (this.searchForm && !this.searchForm.current.contains(event.target)) {
            this.setState({
                isOpenList: false
            })
        }
    }


    getConfig = (categoryId) => {
        let config;

        switch (categoryId) {
            case 1:
                config = {
                    listName: 'liste-des-conseillers-municipaux',
                    origin: 'communeLabel'
                }
                break;
            case 2:
                config = {
                    listName: 'liste-des-conseillers-communautaires',
                    origin: 'communeLabel'
                }
                break;
            case 3:
                config = {
                    listName: 'liste-des-conseillers-departementaux',
                    origin: 'departmentLabel'
                }
                break;
            case 4:
                config = {
                    listName: 'liste-des-conseillers-regionaux',
                    origin: 'areaLabel'
                }
                break;
            case 5:
                config = {
                    listName: 'liste-des-conseillers-corse',
                    origin: 'departmentLabel'
                }
                break;
            case 6:
                config = {
                    listName: 'liste-des-deputes-europeens'
                }
                break;
            case 7:
                config = {
                    listName: 'liste-des-senateurs',
                    origin: 'departmentLabel'
                }
                break;
            case 8:
                config = {
                    listName: 'liste-des-deputes',
                    origin: 'departmentLabel'
                }
                break;
            case 9:
                config = {
                    listName: 'liste-des-maires',
                    origin: 'communeLabel'
                }
                break;
            case 10:
                config = {
                    listName: 'liste-des-presidents-des-departements',
                    origin: 'departmentLabel'
                }
                break;
            default:
                config = {
                    listName: 'liste-des-presidents-des-regions',
                    origin: 'areaLabel'
                }
                break;
        }

        return config;
    }

    onSearch = (e) => {

        let criteria = e.target.value, {
            categoryId, isOpenList
        } = this.state, config, url;

        if (criteria.trim().length > 0) {

            this.setState({
                isLoading: true
            });

            config = this.getConfig()
            url = `/${config.listName}`;
            axios.post(url, {
                categoryId,
                criteria
            }, {
                headers: {'X-Requested-With': 'XMLHttpRequest'},
            })
                .then((response) => {
                    this.setState({
                        isLoading: false,
                        members: response.data,
                        isOpenList: true
                    })
                })
                .catch((error) => {
                    console.log(error);
                    this.setState({
                        isLoading: false
                    })
                });
        } else if (isOpenList) {
            this.setState({
                isOpenList: false
            })
        }
    }

    onChangeCategory = ({value}) => {

        let config = this.getConfig(value);

        this.setState({
            categoryId: value,
            config,
            isDisabled: false
        })
    }

    componentWillUnmount() {
        document.removeEventListener('mousedown', this.closeMemberList);
    }

    onCriteriaFocus = (e) => {

        let criteria = e.target.value;

        if (criteria.trim().length > 0 && !this.state.isOpenList) {
            this.setState({
                isOpenList: true
            });
        }
    }

    closeList = () => {
        this.setState({
            isOpenList: false
        })
    }


    render() {

        const {
            isDisabled, categoryId, members,
            config, isOpenList, isLoading
        } = this.state;

        const strings = [
            "Application destinée à évaluer tous vos élus de proximité et de votre circonscription",
            "Renseignez-vous sur des informations pratiques de votre commune, de votre département et de votre région",
            "Agissons ensemble pour l’accélération d’une démocratie mixte",
            _.upperFirst("éducation, ") + _.upperFirst("économie,") + " Environnement, Logement, Santé"
        ];

        return (
            <div id="home">
                <div className="overlay d-flex flex-column justify-content-center">
                    <div
                        className="d-flex flex-grow-1 align-items-lg-end align-items-center justify-content-center">
                        <div className="container">
                            <h2 className="text-white text-center font-weight-bold home-title"
                                id="home-title">
                                <TypeIt
                                    options={{
                                        speed: 30
                                    }}
                                    getBeforeInit={instance => {
                                        instance
                                            .type(strings[0])
                                            .pause(500)
                                            .delete(strings[0].length)
                                            .type(strings[1])
                                            .pause(500)
                                            .delete(strings[1].length)
                                            .type(strings[2])
                                            .pause(500)
                                            .delete(strings[2].length)
                                            .type(strings[3]);
                                        return instance;
                                    }}
                                />
                            </h2>
                        </div>
                    </div>

                    <div className="d-flex" style={{height: '45%'}}>
                        <div className="container" ref={this.searchForm}>
                            <div className="row">
                                <div className="col d-none d-lg-block desktop-search-form">
                                    <form method={'post'} action={'/'} className={'form-search mt-5'}>
                                        <div className="form-group">
                                            <div className="input-group">
                                                <Select
                                                    options={options}
                                                    styles={customStyles}
                                                    placeholder={'Sélectionnez une catégorie d\'élu'}
                                                    onChange={this.onChangeCategory}
                                                />
                                                <input type="hidden" name={'categoryId'} value={categoryId || ''}/>
                                                <span style={{height: '100%', width: 5, background: 'white'}}></span>
                                                <div className="flex-fill position-relative">
                                                    <input type="text"
                                                           placeholder={
                                                               isDisabled ? 
                                                               "Sélectionnez d'abord une catégorie d'élu" : 
                                                               'Saisissez le nom d\'un élu ou le nom: commune, commune rattachée, département, région'
                                                            }
                                                           className={`form-control rounded-0 ${isDisabled ? 'disabled' : ''}`}
                                                           id={'search_criteria'}
                                                           name={'criteria'}
                                                           autoComplete={'off'}
                                                           onChange={this.onSearch}
                                                           onFocus={this.onCriteriaFocus}
                                                           disabled={isDisabled}
                                                    />
                                                    {
                                                        isOpenList &&
                                                        <div
                                                            className='list-form-search bg-primary border-bottom overflow-hidden'>
                                                            <ul className="list-unstyled m-0 p-0 overflow-auto">
                                                                {
                                                                    members.length > 0 ?
                                                                        members.map((member, index) => (
                                                                            <li key={index}>
                                                                                <form
                                                                                    action={`/${config.listName}/evaluation`}
                                                                                    method="post">
                                                                                    <input type="hidden"
                                                                                           name="categoryId"
                                                                                           value={categoryId}/>
                                                                                    <input type="hidden" name="memberId"
                                                                                           value={member.id}/>
                                                                                    <div
                                                                                        className="form-vote text-decoration-none text-white px-3 py-2 d-block border-bottom"
                                                                                        onClick={this.closeList}
                                                                                    >
                                                                                        {member.firstName + ' ' + member.lastName} {config.origin ? (member[config.origin] ? ' - ' + member[config.origin] : '') : ''}
                                                                                    </div>
                                                                                </form>
                                                                            </li>
                                                                        )) :
                                                                        <li>
                                                                            <span
                                                                                className="text-white text-center p-3 d-block">
                                                                                - Aucun résultat -
                                                                            </span>
                                                                        </li>
                                                                }
                                                            </ul>
                                                        </div>
                                                    }
                                                </div>
                                                <div className="input-group-append">
                                                        <button className={'btn btn-primary'}
                                                            disabled={isDisabled}>
                                                            Rechercher <i className="fa fa-search"></i>
                                                        </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div className="col d-block d-lg-none mobile-search-form">
                                    <form method={'post'} action={'/'} className={'form-search mt-5'}>
                                        <div className="form-group">
                                            <Select
                                                options={options}
                                                styles={customMobileStyles}
                                                placeholder={'Sélectionnez une catégorie d\'élu'}
                                                onChange={this.onChangeCategory}
                                            />
                                            <input type="hidden" name={'categoryId'} value={categoryId || ''}/>
                                        </div>
                                        <div className="form-group position-relative">
                                            <input type="text"
                                                   placeholder={
                                                       isDisabled ?
                                                           "Sélectionnez d'abord une catégorie d'élu" :
                                                           'Saisissez le nom d\'un élu ou d\'une ville'}
                                                   className={`form-control rounded-0 ${isDisabled ? 'disabled' : ''} ${isOpenList ? 'active' : ''}`}
                                                   id={'search_criteria'}
                                                   autoComplete={'off'}
                                                   onChange={this.onSearch}
                                                   onFocus={this.onCriteriaFocus}
                                                   disabled={isDisabled}
                                            />
                                            {
                                                isOpenList &&
                                                <div
                                                    className='list-form-search bg-primary border-bottom overflow-hidden'>
                                                    <ul className="list-unstyled m-0 p-0 overflow-auto">
                                                        {
                                                            members.length > 0 ?
                                                                members.map((member, index) => (
                                                                    <li key={index}>
                                                                        <form
                                                                            action={`/${config.listName}/evaluation`}
                                                                            method="post">
                                                                            <input type="hidden"
                                                                                   name="categoryId"
                                                                                   value={categoryId}/>
                                                                            <input type="hidden" name="memberId"
                                                                                   value={member.id}/>
                                                                            <div
                                                                                className="form-vote text-decoration-none text-white px-3 py-2 d-block border-bottom"
                                                                                onClick={this.closeList}
                                                                            >
                                                                                {member.firstName + ' ' + member.lastName} {config.origin ? (member[config.origin] ? ' - ' + member[config.origin] : '') : ''}
                                                                            </div>
                                                                        </form>
                                                                    </li>
                                                                )) :
                                                                <li>
                                                                            <span
                                                                                className="text-white text-center p-3 d-block">
                                                                                - Aucun résultat -
                                                                            </span>
                                                                </li>
                                                        }
                                                    </ul>
                                                </div>
                                            }
                                        </div>
                                        <div className="form-group">
                                            <button className={'btn btn-primary btn-lg btn-block'}
                                                    disabled={isDisabled}>
                                                Rechercher <i className="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <Slider {...settings} className="home-slider">
                    <div className="home-slider-item">
                        <img className="img-fluid" src="build/images/back-1.3b4558ea.jpg"
                             alt=""/>
                    </div>
                    <div className="home-slider-item">
                        <img className="img-fluid" src="build/images/back-2.3878db8c.jpg"
                             alt=""/>
                    </div>
                    <div className="home-slider-item">
                        <img className="img-fluid" src="build/images/back-3.f4a8ba3a.jpg"
                             alt=""/>
                    </div>
                </Slider>
            </div>
        );
    }
}

export default SearchForm;