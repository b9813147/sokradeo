// import axios from 'axios';
//
// const axiosClient = axios.create({
//     baseURL: `/tba/video/`
// })

// export async function getVideo(tbaId: number) {
//     try {
//         const {data} = await axiosClient.get(`/${tbaId}`);
//         return [null, data];
//     } catch (error) {
//         return [error];
//     }
// }

// export async function updateVideo(tbaId: number) {
//     try {
//         const {data} = await axiosClient.put(`/${tbaId}`)
//         return [null, data];
//     } catch (error) {
//         return [error];
//     }
// }

export default (() => {
  class Service {
    constructor() {
      this.baseUrl = `${window.location.origin}/tba/video/`;
    }

    getBaseUrl() {
      return this.baseUrl;
    }

    async getVideo(tbaId) {
      let url = this.baseUrl + tbaId;
      try {
        return await axios.get(url).then((response) => response.data);
      } catch (err) {
        return err;
      }

    }

    async updateVideo(tbaId, formData, config) {
      let url = this.baseUrl + tbaId ;
      try {
        return await axios.post(url, formData, config);
      } catch (err) {
        return err;
      }
    }
  }

  return new Service();
})();
