import React, {Component} from 'react';
import axios from 'axios';

class FilterForm extends Component {

    constructor(props) {

        super(props);

        this.state = {
            members: [],
            url: null,
            isOpenList: false,
            isLoading: false,
            categoryId: null,
            searchPlaceholder: '',
            criteria: ''
        }

        this.filterForm = React.createRef();
    }

    componentDidMount() {
        let categoryId = parseInt(document.getElementById('filterForm').getAttribute('data-category-id')),
            qs = window.location.search,
            url = window.location.href;


        if(qs.length > 0) {
            url = url.substring(0, url.indexOf(qs));
        }

        this.setState({
            categoryId,
            url,
            searchPlaceholder: this.getPlaceholderByCategory(categoryId),
            origin: this.getOrigin(categoryId)
        })
        document.addEventListener('mousedown', this.closeMemberList);
    }

    resetForm = () => {
        this.setState({
            members: [],
            isOpenList: false,
            criteria: '',
        })
    }

    onSearch = (e) => {
        const {url, categoryId} = this.state;
        let criteria = e.target.value;

        if(criteria.trim().length > 0) {

            this.setState({
                isLoading: true
            })

            axios.post(url, {
                categoryId,
                criteria
            }, {
                headers: {'X-Requested-With': 'XMLHttpRequest'},
            })
                .then((response) => {
                    this.setState({
                        members: response.data,
                        isOpenList: true,
                        isLoading: false
                    });
                })
                .catch((error) => {
                    console.log(error);

                    this.setState({
                        isLoading: false
                    })
                });
        }
        else {
            this.setState({
                isOpenList: false
            });
        }
    }

    componentWillUnmount() {
        document.removeEventListener('mousedown', this.closeMemberList);
    }

    closeMemberList = (event) => {
        if (this.filterForm && !this.filterForm.current.contains(event.target)) {
            this.setState({
                isOpenList: false
            })
        }
    }

    closeList = () => {
        this.setState({
            isOpenList: false
        })
    }

    getPlaceholderByCategory = (categoryId) => {
        let placeholder;
        switch (parseInt(categoryId)) {
            case 1:
                placeholder = 'Saisissez et s??lectionnez le nom d\'une commune ou d\'un(e) conseiller(??re) municipal(e)';
                break;
            case 2:
                placeholder = 'Saisissez et s??lectionnez le nom d\'une commune rattach??e ou d\'un(e) conseiller(??re) communautaire';
                break;
            case 3:
                placeholder = 'Saisissez et s??lectionnez le nom de l\'??lu ou d\'un d??partement ou le num??ro d\'un d??partement';
                break;
            case 4:
                placeholder = 'Saisissez et s??lectionnez le nom d\'un ??lu ou d\'une r??gion';
                break;
            case 5:
                placeholder = 'Saisissez et s??lectionnez le nom et pr??nom d\'un(e) conseiller(??re) Corse';
                break;
            case 6:
                placeholder = 'Saisissez et s??lectionnez le nom et pr??nom d\'un(e) d??put??(e) europ??en(ne)';
                break;
            case 7:
                placeholder = 'Saisissez et s??lectionnez le nom et pr??nom d\'un(e) s??nateur(trice) ou le nom de la circonscription de d??partement';
                break;
            case 8:
                placeholder = 'Saisissez et s??lectionnez le nom et pr??nom d\'un(e) d??put??(e) ou le nom de la circonscription de d??partement';
                break;
            case 9:
                placeholder = 'Saisissez et s??lectionnez le nom d\'une commune ou d\'un(e) Maire';
                break;
            case 10:
                placeholder = 'Saisissez et s??lectionnez le nom de l\'??lu ou d\'un d??partement ou le num??ro d\'un d??partement';
                break;
            default:
                placeholder = 'Saisissez et s??lectionnez le nom d\'un ??lu ou d\'une r??gion';
                break;
        }

        return placeholder;
    }

    getOrigin = (categoryId) => {
        let origin;
        switch (categoryId) {
            case 1:
                origin = 'communeLabel';
                break;
            case 2:
                origin = 'communeLabel';
                break;
            case 3:
                origin = 'departmentLabel';
                break;
            case 4:
                origin = 'areaLabel';
                break;
            case 5:
                origin = 'departmentLabel';
                break;
            case 7:
                origin = 'departmentLabel';
                break;
            case 8:
                origin = 'departmentLabel';
                break;
            case 9:
                origin = 'communeLabel';
                break;
            case 10:
                origin = 'departmentLabel';
                break;
            case 11:
                origin = 'areaLabel'
                break;
            default:
                origin = null;
                break;
        }
        return origin;
    }

    render() {

        const {
            members, url, isOpenList,
            searchPlaceholder, categoryId,
            origin, isLoading
        } = this.state;

        return (
            <div className={'elected-list-form'}  ref={this.filterForm}>
                <div className="form-group position-relative">
                    <div className="input-group">
                        <input type="text" placeholder={searchPlaceholder} className={`d-none d-lg-block form-control form_criteria ${isOpenList && 'active'}`}
                            onChange={this.onSearch}
                        />
                        <input type="text" placeholder={'Recherchez un ??lu ...'} className={`d-block d-lg-none form-control form_criteria ${isOpenList && 'active'}`}
                           onChange={this.onSearch}
                        />
                        <div className="input-group-append">
                            <button type='reset' className='btn btn-danger' onClick={this.resetForm}>
                                <i className="fa fa-times"></i>
                            </button>
                            <span className={`input-group-text text-white border-0 bg-primary ${isOpenList && 'active'}`}>
                                {
                                    isLoading ?
                                        <i className="fa fa-spinner fa-spin"></i>:
                                        <i className="fa fa-search"></i>
                                }
                            </span>
                        </div>
                    </div>

                    <div className={`result-search-list bg-primary border-bottom overflow-hidden ${!isOpenList && 'd-none'}`}>
                        <ul className="list-unstyled m-0 p-0 overflow-auto">
                            {
                                members.length > 0 ?
                                    members.map((member, index) => (
                                        <li key={index}>
                                            <form action={`${url}/evaluation`} method={'post'}>
                                                <input type="hidden" name={'memberId'} value={member.id}/>
                                                <input type="hidden" name={'categoryId'} value={categoryId}/>
                                                <div onClick={this.closeList} className="form-vote text-decoration-none text-white p-3 d-block border-bottom">
                                                    {
                                                        member.firstName + ' ' + member.lastName
                                                    }
                                                    {
                                                        origin ? (member[origin] ? ' - ' + member[origin] : '') : ''
                                                    }
                                                </div>
                                            </form>
                                        </li>
                                    )) :
                                    <li>
                                        <span className="text-white p-3 d-block">
                                            - Aucun r??sultat -
                                        </span>
                                    </li>
                            }
                        </ul>
                    </div>
                </div>
            </div>
        );
    }
}

export default FilterForm;
