import { Message } from "../types"
import useApi from "./useApi"

const useChat = () => {

  const api = useApi()

  const sendMessage = async (message: Partial<Message>) => {
    const { data } = await api.post('/chat-messages', message)
    return data
  }

  const getHistory = async () => {
    const { data } = await api.get('/chat-messages')
    return data
  }

  return {
    sendMessage,
    getHistory,
  }
}

export default useChat