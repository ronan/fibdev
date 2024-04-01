export ZSH=/root/.oh-my-zsh
export HISTFILE=/workspace/data/home/.zsh_history

# See https://github.com/ohmyzsh/ohmyzsh/wiki/Themes
ZSH_THEME="devcontainers"

COMPLETION_WAITING_DOTS="true"
DISABLE_UNTRACKED_FILES_DIRTY="true"

ZSH_CUSTOM=/workspace/.devcontainer/devcontainer/zsh_custom

plugins=(git)

source /root/.oh-my-zsh/oh-my-zsh.sh

path+=(/workspace/.devcontainer/bin)

# Example aliases
# alias zshconfig="mate ~/.zshrc"
# alias ohmyzsh="mate ~/.oh-my-zsh"
DISABLE_AUTO_UPDATE=true
DISABLE_UPDATE_PROMPT=true
