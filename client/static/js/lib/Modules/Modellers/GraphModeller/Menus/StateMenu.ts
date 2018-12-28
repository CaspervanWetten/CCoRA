// /// <reference path='./index.ts'/>

// class StateMenu extends GraphModellerMenu
// {
//     public Drawing : StateDrawing;
//     public constructor(modeller : GraphDrawer, sd : StateDrawing)
//     {
//         super(modeller);
//         this.Drawing = sd;
//     }

//     protected GetElement()
//     {
//         let p = new Popup();

//         let context = this.Modeller.GetElement().getContext("2d");
//         let drawing = this.Drawing;
//         let pos     = drawing.GetPosition();
//         let size    = drawing.GetSize(context);
//         let settings = Settings.GetInstance();
//         let stateHeight = settings.GetStateHeight();

//         let buttons = document.createElement("div");
//         buttons.classList.add("buttons");

//         let editButton = document.createElement("button");
//         editButton.classList.add("confirm");
//         editButton.appendChild(document.createTextNode("Edit"));

//         let connectButton = document.createElement("button");
//         connectButton.classList.add("confirm");
//         connectButton.appendChild(document.createTextNode("Connect"));

//         buttons.appendChild(editButton);
//         buttons.appendChild(connectButton);

//         p.SetBody(buttons);
//         p.SetLeft(pos.X + size.Width / 2);
//         p.SetTop(pos.Y + stateHeight + 5);

//         p.SetElementClassname("editState");

//         return p.Render();
//     }

//     protected GetParent()
//     {
//         return this.Modeller.GetElement().parentElement;
//     }
// }

// // class StateMenu extends ModellerMenu<GraphModeller>
// // {
// //     public constructor(modeller : GraphModeller, sd : StateDrawing)
// //     {
// //         super(modeller);
// //         let context = this.Modeller.GetElement().getContext("2d");
// //         let drawing = sd;
// //         let pos     = drawing.GetPosition();
// //         let size    = drawing.GetSize(context);
// //         let state   = drawing.GetState();
// //         let store   = Store.GetInstance();
// //         let net     = store.GetPetrinet();
// //         let places  = net.GetPlaces().sort();

// //         let settings = Settings.GetInstance();
// //         let sh = settings.GetStateHeight();

// //         let buttons = document.createElement("div");
// //         buttons.classList.add("buttons");
        
// //         let editButton = document.createElement("button");
// //         editButton.classList.add("confirm");
// //         editButton.appendChild(document.createTextNode("Edit"));

// //         let connectButton = document.createElement("button");
// //         connectButton.classList.add("confirm");
// //         connectButton.appendChild(document.createTextNode("Connect"));

// //         buttons.appendChild(editButton);
// //         buttons.appendChild(connectButton);

// //         this.SetBody(buttons);
// //         this.SetLeft(pos.X + size.Width / 2);
// //         this.SetTop(pos.Y + sh + 5);

// //         this.SetElementClassname("editState");
// //     }
// // }