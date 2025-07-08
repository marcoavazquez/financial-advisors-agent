import { useActionState, useState } from "react";
import { Alert, Button, IconButton, Loader } from "../../components";
import { AddIcon, CloseIcon, HubSpotIcon, RefreshIcon } from "../../components/icons";
import { Flex } from "../../components/layout";
import ChatForm from "./components/Form";
import { Message } from "../../types";
import MessageBox from "./components/MessageBox";
import useChat from "../../hooks/useChat";
import useHubspot from "../../hooks/useHubspot";
import useGoogle from "../../hooks/useGoogle";

const thread_id = Math.random().toString(36).slice(2);

const ChatPage = () => {

  const { sendMessage } = useChat()
  const { syncData: syncHubspot } = useHubspot()
  const { syncData: syncGoogle } = useGoogle()

  const [error, setError] = useState<string | null>(null);
  const [messages, setMessages] = useState<Message[]>([]);
  const [synchronizing, setSynchronizing] = useState(false);

  const [, formAction, isPending] = useActionState(async (prev: Message, formData: FormData) => {
      setError(null)
      addMessage({
        id: Date.now(),
        content: formData.get('content') as string,
        is_assistant: false,
        created_at: new Date().toISOString(),
        thread_id,
      })
      try {
        const { data } = await sendMessage({
          content: formData.get('content') as string,
          thread_id,
        })
        addMessage(data)
        return data
      } catch (error) {
        console.error(error)
        setError('Failed to send message. Please try again. ' + error.toString());
        return prev
      }
    }, {})

  const addMessage = (newMessage: Message) => {
    setMessages(prev => [...prev, newMessage]);
  }

  const handleSyncDataClick = () => {
    setSynchronizing(true)
    try {
      syncHubspot()
      syncGoogle()
    } catch (error) {
      console.error(error)
    } finally {
      setSynchronizing(false)
    }
  }

  return (
    <Flex className="chat-page" direction="column" justifyContent="space-between">
      <header>
        <Flex justifyContent="space-between" alignItems="center" padding="1rem 0">
          <h3>Ask Anything</h3>
          <IconButton>
            <CloseIcon />
          </IconButton>
        </Flex>
        <Flex justifyContent="space-between" alignItems="center">
          <Flex gap="0.5rem">
            <Button>Chat</Button>
            <Button variant="secondary">History</Button>
          </Flex>
          <a href="/hubspot/redirect" className="btn btn-outlined">
            <HubSpotIcon /> Connect
          </a>
          <Button
            disabled={synchronizing}
            title="Manual Sync"
            onClick={handleSyncDataClick}
          >
            <RefreshIcon />
          </Button>
          <Button variant="secondary">
            <AddIcon />
            New Thread
          </Button>
        </Flex>
      </header>
      <Flex className="chat-content" flex="1" direction="column" gap="1rem">
        <div className="chat-messages">
          {messages.map((msg) => <MessageBox key={msg.id} message={msg} />)}
        </div>
        {isPending && <Loader />}
        {!!error && <Alert severity="danger">{error}</Alert>}
        <ChatForm formAction={formAction} />
      </Flex>
    </Flex>
  )
}

export default ChatPage;