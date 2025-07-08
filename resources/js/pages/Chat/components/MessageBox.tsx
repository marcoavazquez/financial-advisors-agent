import { Message } from "../../../types"

interface Props {
  message: Message
}

const MessageBox: React.FC<Props> = ({ message }) => {
  return (
    <div className={`chat-message chat-message-${message.is_assistant ? 'assistant' : 'user'}`}>
      <div className={`chat-message-content`}>
        {message.content}
      </div>
    </div>
  )
}

export default MessageBox