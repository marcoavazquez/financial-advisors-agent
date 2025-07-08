import useApi from "./useApi"

const useHubspot = () => {
  const api = useApi()

  const syncData = async () => {
    const { data } = await api.post('/hubspot/sync')
    return data
  }

  return {
    syncData
  }
}
export default useHubspot