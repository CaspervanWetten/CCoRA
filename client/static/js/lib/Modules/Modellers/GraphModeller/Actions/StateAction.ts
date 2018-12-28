abstract class StateAction extends GraphModellerAction
{
    protected ParseStateString(stateString : string, id? : number)
    {
        let k = id != null ? id : GraphModellerAction.ElementCounter;

        let state = new State(k);
        let s = stateString.replace(/\s/g, '');
        if(s != "")
        {
            let pairs = s.split(',');
            for(let i = 0; i < pairs.length; i++) {
                let pair    = pairs[i];
                let k       = pair.split(':');
                let place   = k[0];
                let tokens  = parseInt(k[1], 10);
    
                let t : TokenCount;
                if(isNaN(tokens)) {
                    t = new OmegaToken();
                }
                else {
                    t = new IntToken(tokens);
                }

                state.Add(place, t);
            }            
        }

        if(id == null) GraphModellerAction.ElementCounter++;

        return state;
    }
}