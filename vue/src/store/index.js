import {createStore} from "vuex";
import axiosClient from "../axios.js";


const store = createStore({
  state: {
    user: {
      data: {},
      token: sessionStorage.getItem("TOKEN"),
    },
    surveys: [
      {
        id: 100,
        title: "Technology test",
        slug: "Technology-test",
        status: "draft",
        image: "test",
        description: "description testttttt",
        created_at: "2023-10-20 18:00:00",
        updated_at: "2023-10-20 18:00:00",
        expire_date: "2023-10-22 19:00:00",
        questions: [
          {
            id: 1,
            type: "select",
            question: "Where are you from?",
            description: null,
            data: {
              options: [
                {uuid: "f8afa0a-a3qd1-da8d1-qw0d", text: "USA"},
                {uuid: "f8afa0a-a2qd2-da8d1-qw0d", text: "Georgia"},
                {uuid: "f8afa0a-a3qd1-fa8d1-q10d", text: "Germany"},
                {uuid: "f8afa0a-a3fds-fa8d1-q10d", text: "India"}
              ],
            }
          },
          {
            id: 2,
            type: "checkbox",
            question: "Where are you from?",
            description: "test desc",
            data: {
              options: [
                {uuid: "f8afa0a-a3qd1-da8d1-qw0d", text: "JAVASCRIPT"},
                {uuid: "f81fa0a-a2qd2-da8d1-qw0d", text: "Python"},
                {uuid: "f83fa0a-a3qd1-fa8d1-q10d", text: "Perl"},
                {uuid: "f4afa0a-a3fds-fa8d1-q10d", text: "PHP"}
              ],
            }
          },
          {
            id: 3,
            type: "checkbox",
            question: "Where are you from?",
            description: "test id3",
            data: {
              options: [
                {uuid: "f8af34a-a3qd1-da8d1-qw0d", text: "JAVASCRIPT_id3"},
                {uuid: "f81f54a-a2qd2-da8d1-qw0d", text: "Python_id3"},
                {uuid: "f83f603-a3qd1-fa8d1-q10d", text: "Perl_id3"},
                {uuid: "f4af405-a3fds-fa8d1-q10d", text: "PHP_id3"}
              ],
            },
          },
          {
            id: 4,
            type: "radio",
            question: "Where are you from?",
            description: "test radio",
            data: {
              options: [
                {uuid: "f8af34a-a3qd1-da8d1-qr0d", text: "JAVASCRIPT_radio"},
                {uuid: "f81f54a-a2qd2-da8d1-qq0d", text: "Python_radio"},
                {uuid: "f83f603-a3qd1-fa8d1-qaz0d", text: "Perl_radio"},
                {uuid: "f4af405-a3fds-fa8d1-q1fd", text: "PHP_radio"}
              ],
            }
          },
          {
            id: 5,
            type: "text",
            question: "Text Questions?",
            description: null,
            data: {
              options: []
            }
          },
          {
            id: 6,
            type: "textarea",
            question: "Textarea Questions?",
            description: "not null text area",
            data: {
              options: []
            }
          },
        ],
      },
      {
        id: 200,
        title: "Laravel 1 ",
        slug: "laravel-test",
        status: "active",
        image: "test",
        description: "description testttttt larave",
        created_at: "2023-09-20 18:00:00",
        updated_at: "2023-09-20 18:00:00",
        expire_date: "2023-10-22 19:00:00",
        questions: []
      },
      {
        id: 300,
        title: "Laravel 2 ",
        slug: "laravel-test",
        status: "active",
        image: "test",
        description: "description testttttt larave",
        created_at: "2023-09-20 18:00:00",
        updated_at: "2023-09-20 18:00:00",
        expire_date: "2023-10-22 19:00:00",
        questions: []
      },
      {
        id: 400,
        title: "Laravel  3",
        slug: "laravel-test",
        status: "active",
        image: "test",
        description: "description testttttt larave",
        created_at: "2023-09-20 18:00:00",
        updated_at: "2023-09-20 18:00:00",
        expire_date: "2023-10-22 19:00:00",
        questions: []
      }


    ],
    questionTypes: ['text', 'select', 'radio', 'checkbox', 'textarea']
  },
  getters: {},
  actions: {
    register({commit}, user) {
      return axiosClient.post('/register', user)
        .then(({data}) => {
          commit('setUser', data)
          return data
        })

    },
    login({commit}, user) {
      return axiosClient.post('/login', user)
        .then(({data}) => {
          commit('setUser', data)
          return data
        })
    },
    logout({commit}) {
      return axiosClient.post('/logout')
        .then(response => {
          commit('logout')
          return response;
        })
    },
    saveSurvey({commit}, survey) {
      let response;
      if (survey.id) {
        response = axiosClient
          .put(`/surveys/${survey.id}`, survey)
          .then((res) => {
            commit('updateSurvey', res.data);
            return res;
          });
      } else {
        response = axiosClient
          .post('/surveys', survey)
          .then((res) => {
            commit('saveSurvey', res.data);
            return res;
          })
      }
      return response;
    },
  },

  mutations: {
    logout: state => {
      state.user.data = {};
      state.user.token = null;
      sessionStorage.removeItem('TOKEN');
    },
    setUser: (state, userData) => {
      state.user.token = userData.token;
      state.user.data = userData.user;
      sessionStorage.setItem('TOKEN', userData.token);
    },
    saveSurvey: (state, survey) => {
      state.surveys = [...state.surveys, survey.data];
    },
    updateSurvey: (state, survey) => {
      state.surveys =state.surveys.map((s)=>{
        if (s.id==survey.data.id){
          return survey.data;
        }
        return s;
      });
    },
  },
  modules: {}
})

export default store;
