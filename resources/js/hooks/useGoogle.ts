import useApi from "./useApi"

const useGoogle = () => {
  const api = useApi()

  const syncData = async () => {
    const { data } = await api.post('/google/sync')
    return data
  }

  return {
    syncData
  }
}
export default useGoogle